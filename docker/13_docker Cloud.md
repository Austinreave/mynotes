#### 什么是Docker Cloud

- Docker Cloud 是caas（Container as a Service）容器即服务，阿里云和腾讯云属于paas平台即服务，caas是在paas之上的，我们要提供docker的service，必须要有底层基础设施的支持，paas他们虚拟的计算资源，在这些虚拟资源之上在进行搭建docker的微服务。
- Docker Cloud是提供容器的管理，编排，部署的托管服务。

##### 主要模块

+ image管理：自动构建、自动发布。
+ 关联云服务商，容器服务是运行在云服务器上的（阿里云、亚马逊）。

+ 在云服务器上创建docker节点
  + 创建service（单个docker服务）
  + 创建stack（多个service组合一个复杂的应用）

##### 运行模式

- Standard模式。一个Node就是一个Docker Host
- SWarm模式(beta)。多个Node组成的Swarm Cluster

##### 操作

1. 登录 docker cloud服务商平台
2. 将docker cloud和自己的git仓库进行关联（触发image自动编译）
3. 配置自动部署（步骤5，触发service自动部署）
4. 当修改dockerfile时docker cloud会自动build成新的镜像
5. docker cloud检测到image有修改会自动部署一个新的服务

