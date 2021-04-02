#### server代码逻辑

1. 将grpc服务注册到etcd

```
#EtcdAddr指的是etcd服务的地址，多个用“;”隔开
#ServiceName指的是Gprc服务名称，
#addr指的是grpc服务的ip地址
#5指的是租约5秒过期
etcdservice.Register(EtcdAddr, ServiceName, addr, 5)
```

2. etcd注册服务
   1. 连接etcd服务器
   2. 循环心跳检测
   3. 心跳发生器
   4. KeepAlive
      1. 创建租约（Lease）
      2. 自动续租（KeepAlive）
      3. 清空续租响应消息

```
const schema = "ns"//固定
func Register(etcdAddr, name string, addr string, ttl int64) error {
   var err error
	 //构建etcd连接	
   if cli == nil {
      cli, err = clientv3.New(clientv3.Config{
         Endpoints:   strings.Split(etcdAddr, ";"),
         DialTimeout: 15 * time.Second,
      })
      if err != nil {
         fmt.Printf("connect to etcd err:%s", err)
         return err
      }
   }

	//定时器异步执行
	ticker := time.NewTicker(time.Second * time.Duration(ttl))
	//主要作用保证grpc服务和etcd保持持久连接（断电、断网）
   go func() {
      for {
      	//先进行获取服务名称
         getResp, err := cli.Get(context.Background(), "/"+schema+"/"+name+"/"+addr)
         fmt.Printf("getResp:%+v\n", getResp)
         if err != nil {
            log.Println(err)
            fmt.Printf("Register:%s", err)
            //如果不存在进行注册
         } else if getResp.Count == 0 {
            err = withAlive(name, addr, ttl)
            if err != nil {
               log.Println(err)
               fmt.Printf("keep alive:%s", err)
            }
         } else {
            // fmt.Printf("getResp:%+v, do nothing\n", getResp)
         }

         <-ticker.C
      }
   }()

   return nil
}

func withAlive(name string, addr string, ttl int64) error {
	//创建一个5秒的租约
   leaseResp, err := cli.Grant(context.Background(), ttl)
   if err != nil {
      return err
   }

   //将rpc服务注册到etcd
   _, err = cli.Put(context.Background(), "/"+schema+"/"+name+"/"+addr, addr, clientv3.WithLease(leaseResp.ID))
   if err != nil {
      fmt.Printf("put etcd error:%s", err)
      return err
}

//保持长连接
   ch, err := cli.KeepAlive(context.Background(), leaseResp.ID)
   if err != nil {
      fmt.Printf("keep alive error:%s", err)
      return err
   }

   // 清空 keep alive 返回的channel
   go func() {
      for {
         <-ch
         // ka := <-ch
         // fmt.Println("ttl:", ka.TTL)
      }
   }()
		-l
   return nil
}
```