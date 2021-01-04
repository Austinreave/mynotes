#### images获取方式

1. 通过Dockerfile自定义创建（推荐）

```
#使用当前目录的 Dockerfile 创建镜像，标签为 yuyu/centos:v1。
docker build -t yuyu/centos:v1 . 
#也可以通过 -f Dockerfile 文件的位置：
docker build -f /path/to/a/Dockerfile .
```

1. 从镜像仓库获取

```
#yuyu代表第三方镜像,如果没有则是官方提供的,3.2.4指的是要拉取的版本，如果不写则默认是最新的版本等同于latest
docker pull yuyu/mongo:3.2.4
```

#### images操作

```
docker images #列出本地主机上的镜像
docker search httpd #查找镜像
docker pull httpd #拉取镜像
docker rmi hello-world #删除镜像
docker rmi $(docker images -q) #删除全部的images
docker rmi -f $(docker images -q) #强制删除全部的images
docker rmi $(docker ps -q) # 删除全部未使用的镜像
```

