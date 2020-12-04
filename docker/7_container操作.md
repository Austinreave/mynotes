#### RUN 命令

```
docker run -itd ubuntu /bin/bash #放在镜像名后的是命令，/bin/bash：就是shell等待你的输入,并且执行你提交的命令；-it提供终端并且交互式操作，-d后台运行

docker run -P training/webapp go run main.go #将容器内部使用的网络端口随机映射到我们使用的主机上。大P和小p的区别是:P系统走自己默认的ip和主机映射，p是自定义ip和主机映射

docker run --name="nginx-l" b243c32535da7#为容器指定一个名称

docker run --memory=200M b750bbbcfd88 --vm 1 --verbose --vm-bytes 500M 
--memory 给容器分配内存；
--verbose 查看看打印详细信息；
--vm 1启动一个进程； 
--vm-bytes 500M 给进程分配内存 
注意:进程内存不能大于容器内存

docker run --cpu-shares=10 b750bbbcfd88 --cpu 1 #分配cpu权权重
```

#### 容器操作

```
docker ps -a/al#查看所有的容器/查看运行的容器

docker start/stop/restart b750bbbcfd88 #启动容器/停止容器/重启容器

docker exec -it 243c32535da7 /bin/bash #进入容器

docker rm -f 1e560fca3906 #删除容器

docker rm $(docker ps -a -q) #删除所有容器

docker commit -a "yuyu" -m "php" a404c6c174a2 mymysql:v1 #将容器a404c6c174a2 保存为新的镜像,并添加提交人信息和说明信息; -a :提交的镜像作者；-m :提交时的说明文字；

docker logs -f 1e560fca3906 #查看容器里的应用程序输出的日志信息
```
