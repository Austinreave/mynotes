#多阶段构建从17.05开始支持，当前镜像别名为builder
FROM golang AS builder
#定义参数
ARG ENVARG
#定义工作目录，镜像各层的工作目录为当前目录
WORKDIR /go/src

#下载程序以来的所有包及构建生成程序
RUN git clone http://zhengxiuming:zheng0513ming@39.96.27.29/zhengxiuming/shop_mobile_back.git \
&& cd shop_mobile_back \
&& CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -ldflags "-s -w" -a -v\
&& mkdir -p /release/conf \
&& cp -r shop_mobile_back /release \
&& cp -rf ./conf/$ENVARG/. /release/conf

#二级镜像，真实需要的
FROM golang

WORKDIR /app

#变量程序名称
COPY --from=builder /release  .
#创建logs目录
RUN mkdir -p /app/logs
EXPOSE 9193

ENTRYPOINT ["./shop_mobile_back"]