#### 概念

+ 主要用于托管容器镜像，和我们的gitHub一个性质，类比操作gitHub的思路操作dockerHub即可

#### 提交镜像到Docker hub

1. 到https://hub.docker.com/去注册属于你自己的帐号
2. 提交镜像格式: 执行 `docker commit -a "作者" -m "描述" "本地镜像id" 账号/镜像:版本`

```
//这个跟git的其实是一样的,先提交镜像到本地,才能推送到你的远程镜像仓库,
//一定要注意提交的镜像名格式 帐号/名字:如 user/nginx:v1.0,否则无法推送
docker commit -a "user" -m "test commit" 30740bffc489 user/nginx:v1.0
```

3. 执行命令:`docker login` 登录你的 hub.docker 帐号
4. 推送: `docker push user/nginx:v1.0`
5. 到https://cloud.docker.com/进行查看即可

#### 提交Dockerfile到Docker hub

+ 将dockerHub和gitHub进行关联（dockerHub里面有专门的设置）
+ 在gitHub上建一个代码仓库，专门用于存放本地的Dockerfile
+ dockerHub只要发现gitHub有Dockerfile，dockerHub后台就会自动build一个image
+ 这样我们只需要维护Dockerfile即可

#### 搭建私有的Docker仓库（类比gitLab）

+ Docker 官方提供了一个搭建私有仓库的镜像 registry ，只需把镜像下载下来，运行容器并暴露5000端口，就可以使用了。

  ```
  docker pull registry:2
  docker run -d -v /opt/registry:/var/lib/registry -p 5000:5000 --name myregistry registry:2
  ```

+ 要通过docker tag将该镜像标志为要推送到私有仓库：

  ```
  docker tag nginx:latest localhost:5000/nginx:latest
  ```

+ 通过 docker push 命令将 nginx 镜像 push到私有仓库中

  ```
  docker push localhost:5000/nginx:latest
  ```

+ 访问 http://127.0.0.1:5000/v2/_catalog 查看私有仓库目录，可以看到刚上传的镜像了：

+ 下载私有仓库的镜像，使用如下命令：

  ```
  docker pull localhost:5000/nginx:latest
  ```

#### harbor 的搭建(推荐)

+ docker 官方提供的私有仓库 registry，用起来虽然简单 ，但在管理的功能上存在不足。 Harbor是一个用于存储和分发Docker镜像的企业级Registry服务器，harbor使用的是官方的docker registry(v2命名是distribution)服务去完成。harbor在docker distribution的基础上增加了一些安全、访问控制、管理的功能以满足企业对于镜像仓库的需求。