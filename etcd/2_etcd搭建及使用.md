#### etcd搭建

```
version: '3.5'
services:
  etcd1:
    image: quay.io/coreos/etcd
    container_name: etcd1
    restart: always
    volumes:
      - ./data:/etcd-data
    command: etcd -name etcd1 - --data-dir=/etcd-data -advertise-client-urls http://0.0.0.0:2379 -listen-client-urls http://0.0.0.0:2379 -listen-peer-urls http://0.0.0.0:2380 -initial-cluster-token etcd-cluster -initial-cluster "etcd1=http://etcd1:2380" -initial-cluster-state new
    ports:
      - 2379:2379
      - 2380:2380
    networks:
      - byfn
networks:
  byfn:

#/usr/local/bin/etcd #启动命令
#data-dir=/etcd-data  #指定节点的数据存储目录，这些数据包括节点ID，集群ID，集群初始化配置
#name etcd # 节点名称
#initial-advertise-peer-urls http://${NODE}:2380 #告知集群其他节点url
#listen-peer-urls http://0.0.0.0:2380 #监听URL，用于与其他节点通讯
#advertise-client-urls http://${NODE1}:2379 #告知客户端url, 也就是服务的url
#listen-client-urls http://0.0.0.0:2379 #监听客户端地址
#initial-cluster etcd=http://${NODE1}:2380 #集群中所有节点
```

#### etcd使用

```
#就是很简单的key value
etcdctl put "hello" "world"
etcdctl get "hello"

# 顺序存储的键可以使用前缀模糊查询，目的是：比如查询订单服务的所有接口，不用一个一个的查询
etcdctl put "/users/user1" "zs"
etcdctl put "/users/user2" "ls"
etcdctl get "/users/" --prefix   # 查询全部该
etcdctl del "/users/user2"

//watch机制的作用是和服务端和客户端建立一个socket链接，当服务端数据发生变化时将消息推送给客户端

#客户端1
watch "/users/" --prefix    # 监听该前缀数据变化，此时另起命令行操作数据，则当前命令行能监听到
PUT
/users/add
Hello1

#客户端2
etcdctl put  /users/add Hello1 watch
```
