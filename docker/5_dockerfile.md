#### 什么是dockerfile

dockerfile就是自己制作一个镜像，这个文件里包含了制作的详细步骤，当然制作的语法要按照一定的规范，但是主要的命令还是linux的命令

#### 编写Dockerfile注意

+ 当在进行build的时候出现错误，可以通过 `docker run -it 出现错误的id /bin/bash`进行查看
+ 编写Dockerfile主要就是环境准备、代码准备、端口映射、运行程序

#### 制作镜像语法

- FROM （指定基础的镜像image）

  ```
  格式：
  　　FROM <image>
  　　FROM <image>:<tag>
  示例：
  　　FROM ubuntu:12.04
  注：第一条指令必须为 FROM 指令。所有镜像的祖先都是使用的scratch，尽量使用官方的images作为base images
  ```

- MAINTAINER （用来指定镜像创建者信息）

  ```
  格式：
  	MAINTAINER <name>
  示例：
  	MAINTAINER fendo fendo <fendo@163.com>
  ```

+ RUN（用于在构建镜像时执行命令)

  ```
  格式：
  		RUN <command>
  示例：
  		RUN yum update && yum install -v vim \
  		python-dev
  注：
  		1、基础镜像会把不是核心的功能去掉（比如vim、ifconfig等等）
  		2、通过RUN可以指定自己需要的功能；
  		3、RUN指令创建的中间镜像会被缓存，并且每一层会缓存。并会在下次构建中使用。
  		4、在构建镜像时是一层依赖一层的，最终构建完成，所以之前的每一层就没有什么用了。
  		5、如果不想使用这些缓存镜像，可以在构建时指定--no-cache参数，如：docker build --no-cache 。
  		6、编写时尽量将多个命令写在一行，这样在构建时只用一层节省资源。
  ```

+ WORKDIR（指定工作目录）

  ```
  格式：
      WORKDIR <DIR>
  示例：
      WORKDIR /a  (这时工作目录为/a)
      WORKDIR b  (这时工作目录为/a/b)
      WORKDIR c  (这时工作目录为/a/b/c)
  说明:
      为后续的RUN、CMD或者ENTRYPOINT指定工作目录
  注：
  　　1、通过WORKDIR设置工作目录后，Dockerfile中其后的命令RUN、CMD、ENTRYPOINT、ADD、COPY等命令都会在该目录下执行。
     2、在使用docker run运行容器时，可以通过-w参数覆盖构建时所设置的工作目录。
     3、用WORKDIR，不要用RUN cd 尽量使用绝对目录
  ```

+ ADD和COPY

  ```
  COPY作用：
  		将本地目录拷贝到容器中
  ADD和COPY区别：
  		如果执行ADD，文件是可识别的压缩格式时，则docker会帮忙解压缩
  格式：
      ADD <src>... <dest>
  示例：
  		WORKDIR /a 
      ADD php test/     # 添加 "test" 到 /a/test/php
      ADD curl http://example.com/foobar test/ #添加远程文件或者目录请使用curl或者wget
  注：
  		大部分情况下，COPY优于ADD,ADD除了COPY还有额外解压功能
  ```

+ ENV（设置环境变量）

  ```
  格式：
      ENV <key> <value>
  示例：
      ENV MYSQL_VERSION 5.6
      RUN apt-get install -y mysql-server = "${MYSQL_VERSION}"
  注：
  		这个环境变量可以在后续的任何RUN指令中使用，这就如同在命令前面指定了环境变量前缀一样；也可以在其它指令中直接使用这些环境变量
  ```

+ EXPOSE(容器端口的外放)

  ```
  格式：
      EXPOSE <port> [<port>...]
  示例：
      EXPOSE 80 443
  注：
  		1、这只是一个声明在运行时并不会因为这个声明应用就会开启这个端口的服务，在docker run -P时，会自动随机映射 EXPOSE的端口。
  		2、帮助镜像使用者理解这个镜像服务的守护端口，以方便配置映射。
  ```

+ ENTRYPOINT和CMD

  ```
  作用：
  		1、ENTRYPOINT和CMD都是让用户指定一个可执行程序, 这个可执行程序在container启动后自动启动。
  		2、运行一个没有调用ENTRYPOINT或者CMD的docker镜像, 一定返回错误。
  区别：
  		1、CMD容器启动时需要操作的命令，可以被覆盖。【CMD ['/bin/bash'] 】docker run -it image 和 docker run -it image /bin/bash执行效果一样，都进入了容器。
  		2、ENTRYPOINT容器启动时需要操作的命令，不可以被覆盖。
  实例：
  		CMD ["p in cmd"]
  		ENTRYPOINT ["echo"]
  注：
  	实际工作中将ENTRYPOINT和CMD结合使用效果会更好，ENTRYPOINT将CMD接收到的参数进行执行
  ```

  

