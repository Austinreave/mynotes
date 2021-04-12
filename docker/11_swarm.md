#### 什么是Docker Swarm

+ Docker Swarm是一个可以让容器部署在不同的服务器上，并且统一进行管理的工具。
+ 在微服务使用场景中，比如订单服务、商品服务、用户服务、分别部署不同的服务器上运行，通常的做法是分别在每台服务器上运行docker-compose脚本进行部署，相当浪费时间。
+ 使用Docker Swarm只需要创建一个**mananger**，然后让不同机器加入这个**mananger**，然后统一在**mananger**执行所有服务的启动脚本即可，**mananger**会自动将容器放在不同的节点上运行，只需要通过**mananger**管理所有的服务。

#### Docker Swarm 基本结构图

如下图所示，swarm 集群由管理节点（manager）和工作节点（work node）构成。

- **swarm mananger**：负责整个集群的管理工作包括集群配置、服务管理等所有跟集群有关的工作。
- **work node**：即图中的 available node，主要负责运行相应的服务来执行任务（task）。

![services-diagram](./images/services-diagram.png)

#### 使用步骤

1. 创建 swarm 集群管理节点（manager）

2. 初始化 swarm 集群，进行初始化的这台机器，就是集群的管理节点

   ```
   docker swarm init --advertise-addr 192.168.205.10 #这里的 IP 为创建机器时分配的 ip。
   ```

   ![swarm2](./images/swarm2.png)

   以上输出，证明已经初始化成功。需要把以下这行复制出来，在增加工作节点时会用到：

   ```
   docker swarm join --token SWMTKN-1-6beh04s14g0dw2jdcwhywwkzsmoa0xr7rmj5pxmhf33f074er4-5gxaicgptdr85km0x81nb1aym 192.168.205.10:2377
   ```

3. 创建 swarm 集群工作节点（worker）

   这里直接创建好俩台机器，swarm-worker1 和 swarm-worker2 。

   ![swarm3](./images/swarm3.png)

   分别进入两个机器里，指定添加至上![swarm4](./images/swarm4.png)一步中创建的集群，这里会用到上一步复制的内容。

4. 查看集群信息

   进入管理节点，执行：docker info 可以查看当前集群的信息。

   ![4c9bd24e473fb9c39d391057e177338](./images/4c9bd24e473fb9c39d391057e177338.png)

   通过画红圈的地方，可以知道当前运行的集群中，有三个节点，其中有一个是管理节点。

5. 部署服务到集群中

   **注意**：跟集群管理有关的任何操作，都是在管理节点上操作的。在swarm 中启动容器用docker service ，类似docker run 。

   以下例子，在一个工作节点上创建一个名为 helloworld 的服务，这里是随机指派给一个工作节点

   ```
   docker@swarm-manager:~$ docker service create --replicas 1 --name helloworld alpine ping docker.com
   ```

6. 查看服务部署情况

   ```
   docker service ls #查看当前所有服务
   docker service ps name #查看服务分部情况
   ```

   查看 helloworld 服务运行在哪个节点上，可以看到目前是在 swarm-worker1 节点：

   ![swarm7](./images/swarm7.png)

 7. 扩展集群服务

    我们将上述的 helloworld 服务扩展到俩个节点。

    ```
    docker@swarm-manager:~$ docker service scale helloworld=2
    ```

    ![swarm10](./images/swarm10.png)

8. 删除服务

   ```
   docker@swarm-manager:~$ docker service rm helloworld
   ```

#### 集群服务间通信之Routing Mesh

Routing Mesh 两种体现

+ Internal
  1. 当使用 docker service创建容器服务时会默认创建一条DNS记录 
  2. DNS记录的是虚拟IP(VIP)，为了在扩展集群时定位到不同服务之间的ip地址进行通信
  3. VIP所指的服务ip是通过LVS处理的（自动负载均衡）

+ Ingress
  1. 如果服务有绑定端口，则此服务可以通过任意swarm节点相应接口访问【三个节点都能访问】

#### Docker Stack部署

+ Docker Stack的作用就是在swarm进行部署、设置服务器方便管理(集群管理工具)
+ Docker stack会忽略“构建”指令，无法使用stack命令构建新镜像，它是需要镜像是预先已经构建好的。
+ 部署步骤
  1. 编写docker-compose.yml 遵循deploy语法规则即可
  2. 部署命令：docker stack deploy wordpress --compose-file=docker-compose.yml
+ Docker Stack部署分销商城

  1. 搭建好swarm集群分别为manager、user、goods、order节点
  2. 编写compose.yaml文件
     1. 不能build只能从远程拉取，需要提前准备好镜像
     2. 通过deploy参数设置每个节点，部署的位置，manager节点不做任何服务部署，只负责管理。
     3. 通过docker stack deploy shop --compose-file=docker-compose.yml部署即可。

+ 查看命令

  ```
  #查看集群服务个数
  docker stack ls
  #查看某个集群部署个数
  docker stack services name
  ```