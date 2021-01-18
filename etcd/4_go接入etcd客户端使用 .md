#### client代码逻辑

1. 创建etcd解析器（Resolver）
2. 解析器注册到grpc/resolver
3. 使用名称拨号：scheme://authority/endpoint_name

4. 应用负载均衡策略
5. 获取服务端连接
6. 改造代码，无限循环调用服务

##### 客户端连接服务器

```go
#EtcdAddr etcd服务端
r := etcdservice.NewResolver(EtcdAddr) 
resolver.Register(r)
#ServiceName : grpc服务名称
#grpc.WithBalancerName("round_robin") : 应用负载均衡策略
conn, err := grpc.Dial(r.Scheme()+"://author/"+ServiceName, grpc.WithBalancerName("round_robin"), grpc.WithInsecure())
```

##### etcd名称解析器

1. 类成员设计，全局变量
2. 实现Build方法
   1. 连接etcd服务器
   2. 保存连接给watch使用
   3. 启动watch

```
// 连接etcd
cli, err = clientv3.New(clientv3.Config{
Endpoints:   strings.Split(r.rawAddr, ";"),
DialTimeout: 15 * time.Second,
})

//使用watch机制进行长连接事实监控服务端的消息
go r.watch("/" + target.Scheme + "/" + target.Endpoint + "/")

//watch
func (r *etcdResolver) watch(keyPrefix string) {
	var addrList []resolver.Address //存放rpc服务地址的切片
	//获取rpc址
	getResp, err := cli.Get(context.Background(), keyPrefix, clientv3.WithPrefix())
	if err != nil {
		log.Println(err)
	} else {//如果有添加到切片
		for i := range getResp.Kvs {
			addrList = append(addrList, resolver.Address{Addr: strings.TrimPrefix(string(getResp.Kvs[i].Key), keyPrefix)})
		}
	}

	r.cc.NewAddress(addrList)
	//实时监控如果有修改则进行更新切点地址
	rch := cli.Watch(context.Background(), keyPrefix, clientv3.WithPrefix())
	for n := range rch {
		for _, ev := range n.Events {
			addr := strings.TrimPrefix(string(ev.Kv.Key), keyPrefix)
			switch ev.Type {
			case mvccpb.PUT:
				if !exist(addrList, addr) {
					addrList = append(addrList, resolver.Address{Addr: addr})
					r.cc.NewAddress(addrList)
				}
			case mvccpb.DELETE:
				if s, ok := remove(addrList, addr); ok {
					addrList = s
					r.cc.NewAddress(addrList)
				}
			}
			//log.Printf("%s %q : %q\n", ev.Type, ev.Kv.Key, ev.Kv.Value)
		}
	}
}
```