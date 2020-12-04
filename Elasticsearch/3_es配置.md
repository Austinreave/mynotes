##### 集群配置参数

```
#一个集群名称，在该集群下的所有节点都会自动分配数据共享数据，达到负载均分的作用
cluster.name: "docker-cluster"
```

##### 节点配置参数

```
#每一个节点都有自己的一个名称，仅此而已
node.name: "Franz Kafka"
```

##### 主从配置参数

```
#允许一个节点是否可以成为一个master节点,es是默认集群中的第一台机器为master,如果这台机器停止就会重新选举master.
node.master: true
#允许该节点存储数据(默认开启)
node.data: true
```

配置文件中给出了三种配置高性能集群拓扑结构的模式,如下：

```
#1、如果你想让节点从不选举为主节点,只用来存储数据,可作为负载器
node.master: false
node.data: true
#2、如果想让节点成为主节点,且不存储任何数据,并保有空闲资源,可作为协调器
node.master: true
node.data: false
#3、如果想让节点既不称为主节点,又不成为数据节点,那么可将他作为搜索器,从节点	中获取数据,生成搜索结果等
node.master: false
node.data: false
```

##### 索引配置参数

```
#设置索引的分片数,默认为5，每个节点都可以进行分片，将数据平均分配，达到负载均衡作用，是在创建索引生成后续不能修改
index.number_of_shards: 5
#设置索引的副本数,默认为1，每个分片都会为其创建一个副本，用来作为备份，也可以起到查询作用，提高性能，后续可以修改
index.number_of_replicas: 1
```

##### 路径配置参数

```
#配置文件存储位置
path.conf: /path/to/conf
#数据存储位置(所有数据都存在这一个目录下)
path.data: /path/to/data
#多个数据存储位置,有利于性能提升（将所有数据分别存在不同目录下）
path.data: /path/to/data1,/path/to/data2
#临时文件的路径
path.work: /path/to/work
#日志文件的路径
path.logs: /path/to/logs
#插件安装路径 ik
path.plugins: /path/to/plugins
```

##### 内存配置参数

```
#当JVM开始写入交换空间时（swapping）ElasticSearch性能会低下,你应该保证它不会写入交换空间，设置这个属性为true来锁定内存,同时也要允许elasticsearch的进程可以锁住内存
bootstrap.mlockall: true 
```

##### 网络配置参数

```
#设置绑定的ip地址,可以是ipv4或ipv6的,默认为0.0.0.0
network.bind_host: 192.168.0.1
#设置其它节点和该节点交互的ip地址,如果不设置它会自动设置,值必须是个真实的ip地址
network.publish_host: 192.168.0.1
#同时设置bind_host和publish_host上面两个参数
network.host: 192.168.0.1
#设置节点间交互的tcp端口,默认是9300
transport.tcp.port: 9300
#设置是否压缩tcp传输时的数据，默认为false,不压缩
transport.tcp.compress: true
#设置对外服务的http端口,默认为9200
http.port: 9200
#设置请求内容的最大容量,默认100mb
http.max_content_length: 100mb
#使用http协议对外提供服务,默认为true,开启
http.enabled: false
```

##### 网关配置参数

```
#gateway的类型,默认为local即为本地文件系统,可以设置为本地文件系统
gateway.type: local
#下面的配置控制怎样以及何时启动一整个集群重启的初始化恢复过程(当使用shard gateway时,是为了尽可能的重用local data(本地数据))一个集群中的N个节点启动后,才允许进行恢复处理
gateway.recover_after_nodes: 1
#设置初始化恢复过程的超时时间,超时时间从上一个配置中配置的N个节点启动后算起
gateway.recover_after_time: 5m
#设置这个集群中期望有多少个节点.一旦这N个节点启动(并且recover_after_nodes也符合),立即开始恢复过程(不等待recover_after_time超时)
gateway.expected_nodes: 2
```

##### 集群发现配置参数

```
#设置这个参数来保证集群中的节点可以知道其它N个有master资格的节点.默认为1,对于大的集群来说,可以设置大一点的值(2-4)
discovery.zen.minimum_master_nodes: 1
#探查的超时时间,默认3秒,提高一点以应对网络不好的时候,防止脑裂
discovery.zen.ping.timeout: 3s
#当多播不可用或者集群跨网段的时候集群通信还是用单播吧
discovery.zen.ping.multicast.enabled: false
#这是一个集群中的主节点的初始列表,当节点(主节点或者数据节点)启动时使用这个列表进行探测
discovery.zen.ping.unicast.hosts: ["host1", "host2:port"]
```

##### 登录校验配置

```
http.cors.enabled: true
http.cors.allow-origin: "*"
http.cors.allow-headers: Authorization
xpack.security.enabled: true
xpack.security.transport.ssl.enabled: true
```

































