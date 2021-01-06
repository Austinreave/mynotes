#### 1、编写docker-compose.yml文件

```
version: '3'
services:
  elasticsearch:
    image: elasticsearch:7.1.0
    container_name: elasticsearch
    environment:
      - discovery.type=single-node
    volumes:
      - ./elasticsearch7/plugins:/usr/share/elasticsearch/plugins #插件文件挂载，ik分词器存放即可
      - ./elasticsearch7/data:/usr/share/elasticsearch/data #数据文件挂载
      - ./elasticsearch7/config/elasticsearch.yml:/usr/share/elasticsearch/config/elasticsearch.yml #配置文件挂载
    networks:
      - efknet
    ports:
      - 9200:9200
  kibana:
    image: kibana:7.1.0
    container_name: kibana7
    depends_on:
      - elasticsearch #kibana在elasticsearch启动之后再启动
    volumes:
      - ./kibana7/config/kibana.yml:/usr/share/kibana/config/kibana.yml
    networks:
      - efknet
    ports:
      - 5601:5601
networks:
  efknet:
```

#### 2、编写elasticsearch.yml文件

```
cluster.name: "docker-cluster"
node.name: node-1
network.host: 0.0.0.0
http.port: 9200
#配置es需要登录授权
http.cors.enabled: true
http.cors.allow-origin: "*"
http.cors.allow-headers: Authorization
xpack.security.enabled: true
xpack.security.transport.ssl.enabled: true
```

#### 3、编写kibana.yml文件

```
elasticsearch.hosts: http://elasticsearch:9200
server.host: "0.0.0.0"
server.name: kibana
xpack.monitoring.ui.container.elasticsearch.enabled: true
i18n.locale: zh-CN #中文
#Elasticsearch 设置了基本的权限认证，该配置项提供了用户名和密码，用于 Kibana 启动时维护索引。Kibana 用户仍需要 Elasticsearch 由 Kibana 服务端代理的认证。
elasticsearch.username: "elastic"
elasticsearch.password: "admin888"
```

#### 4、编排容器服务及生成账号密码

```
//编排
docker-compose up -d
//进入es容器
docker exec -it elasticsearch bin/bash
//设置用户名和密码
elasticsearch-setup-passwords interactive
```

#### 5、权限管理

可以对es访问索引的权限进行设置

##### 修改es账户密码方式：

1. 通过curl修改（可以修改elastic账户密码）

   ```
   curl -H "Content-Type:application/json" -XPOST -u elastic 'http://127.0.0.1:9200/_xpack/security/user/elastic/_password' -d '{ "password" : "123456" }'
   ```

2. 在kibana里的权限管理界面修改（不可以修改elastic账户密码）



























