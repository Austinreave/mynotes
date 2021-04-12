##### 什么是DevOps

+ 强调的是高效组织团队之间如何通过自动化的工具协作和沟通来完成软件的生命周期管理，从而更快、更频繁地交付更稳定的软件

##### DevOps工作流程

![20190116142724122](./images/20190116142724122.png)

##### 操作过程描述

1. 开发人员在修改完代码并且本地测试没有问题
2. 提交代码到远程分支
3. 触发Git Hooks、GitLab CI持续集成以及使用Jenkins实现自动化任务
4. Jenkins在测试服务器上进行安装依赖、运行测试、编译、部署测试服务器、部署生产服务器等流程
5. 生成测试结果
6. 运维人员根据测试结果判定是否需要合并到master
7. 当代码合并完之后
8. 此时修改dockerfile时docker cloud会自动build成新的镜像
9. docker cloud检测到image有修改会自动部署一个新的服务
10. Devops流程部署完毕

#### 中小型项目部署流程

1. 开发人员在修改完代码并且本地测试没有问题

2. 提交代码到远程代码仓库，并触发Git Hooks

3. Git Hooks触发sheel脚本

   1. 重新拉取最新代码
   2. 重新编译生成二进制

4. Dockerfile文件将新编译的二进制文件拷贝到容器即可

   ```
   FROM 39.96.27.29:5000/library/golang:runner
   
   WORKDIR /app
   #变量程序名称
   COPY ./conf.yaml  ./conf/
   COPY ./mall_common_cmd  .
   COPY ./apiclient_cert.p12  .
   EXPOSE 4040
   
   ENTRYPOINT ["./mall_common_cmd"]
   ```

5. 编写的compose文件

   ```
   version: "3.5"
   services:
     dbc_mall_common:
       restart: always
       build:
         context: .
         args:
           ENVARG: dev
         dockerfile: Dockerfile
       image: mall_common_image:dev
       ports:
         - 4040:4040
       networks:
         - diagnosis_network
   
   networks:
     diagnosis_network:
       driver: bridge
   ```
   
6. 运维人员通过Makefile部署对应的容器服务即可

   ```sh
   common_dev:
   	#git pull origin develop 也可以通过Git Hooks触发sheel脚本重新拉取最新代码代替
   	CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -o #宿主机进行编译
   	docker-compose up --build -d #重新编译更新docker compose
   	docker image prune -f #清理不再使用的docker镜像
   ```

