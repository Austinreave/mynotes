#### 在mac创建一台虚拟机

```shell
vagrant init centos/7 //创建Vagrantfile
vagrant up //通过agrantfile生成虚拟机
vagrant ssh //进入虚拟机（centos/7）
vagrant status //当前运行的虚拟机
vagrant halt  //停止当前虚拟机
vagrant destory //删除当前虚拟机
```

#### 安装步骤

##### 1. 卸载旧版本

```
$ sudo yum remove docker \
docker-client \
docker-client-latest \
docker-common \
docker-latest \
docker-latest-logrotate \
docker-logrotate \
docker-engine
```

##### 2.使用 Docker 仓库进行安装

1. 设置仓库前需要依赖的软件包

   ```
   sudo yum install -y yum-utils \
   device-mapper-persistent-data \
   lvm2
   ```

2. 设置仓库（下载Docker环境地址）

   ```
   #使用官方源地址（比较慢）
   sudo yum-config-manager \
   --add-repo \
   https://download.docker.com/linux/centos/docker-ce.repo
   
   #阿里云
   sudo yum-config-manager \
   --add-repo \
   http://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo
   
   #清华大学源
   sudo yum-config-manager \
   --add-repo \
   https://mirrors.tuna.tsinghua.edu.cn/docker-ce/linux/centos/docker-ce.repo
   ```

##### 3. 安装 Docker

```
sudo yum install docker-ce docker-ce-cli containerd.io
```

##### 4. 配置开机自启

```
sudo systemctl daemon-reload
sudo systemctl restart docker
```

##### 5.配置镜像加速器

我们国内使用官方Docker Hub仓库实在是太慢了，很影响效率

使用命令编辑文件：

```shell
vim /etc/docker/daemon.json
```

加入下面的数据：

如果你是腾讯云的服务器那么请加入：

```shell
{
  "registry-mirrors": ["https://mirror.ccs.tencentyun.com"]
}
```

如果你是阿里云的服务器那么请加入：

```shell
{
  "registry-mirrors": ["https://aiyf7r3a.mirror.aliyuncs.com"]
}
```

执行命令生效：

```shell
systemctl daemon-reload
systemctl restart docker
```