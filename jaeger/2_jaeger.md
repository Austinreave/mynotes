#### jaeger架构

![Architecture](img/architecture-v1.png)

##### jaeger组件介绍：

+ jaeger-client：jaeger 的客户端，实现了opentracing协议；
+ jaeger-agent：jaeger client的一个代理程序，client将收集到的调用链数据发给agent，然后由agent发给collector；
+ jaeger-collector：负责接收jaeger client或者jaeger agent上报上来的调用链数据，然后做一些校验，比如时间范围是否合法等，最终会经过内部的处理存储到后端存储；
+ jaeger-query：专门负责调用链查询的一个服务，有自己独立的UI；
+ jaeger-ingester：中文名称“摄食者”，可用从kafka读取数据然后写到jaeger的后端存储，比如Cassandra和Elasticsearch；
+ spark-job：基于spark的运算任务，可以计算服务的依赖关系，调用次数等；

#### Trace & Span

![Traces And Spans](img/spans-traces.png)

#### docker-compose安装

````
version: '3'
services:
  collector:
    image: jaegertracing/jaeger-collector:1.18
    container_name: collector
    restart: always
    environment:
      - SPAN_STORAGE_TYPE=elasticsearch
      - ES_SERVER_URLS=http://192.168.124.21:9200
      - ES_USERNAME=elastic
      - ES_PASSWORD=admin888
      - LOG_LEVEL=debug
    networks:
      - jaeger
    ports:
      - "14269"
      - "14268:14268"
      - "14267"
      - "14250:14250"
      - "9411:9411"

  agent:
    image: jaegertracing/jaeger-agent:1.18
    container_name: agent
    restart: always
    environment:
      - REPORTER_GRPC_HOST_PORT=collector:14250
      - LOG_LEVEL=debug
    ports:
      - "5775:5775/udp"
      - "5778:5778"
      - "6831:6831/udp"
      - "6832:6832/udp"
    networks:
      - jaeger
    depends_on:
      - collector
  query:
    image: jaegertracing/jaeger-query:1.18
    restart: always
    container_name: query
    environment:
      - SPAN_STORAGE_TYPE=elasticsearch
      - ES_SERVER_URLS=http://192.168.124.21:9200
      - ES_USERNAME=elastic
      - ES_PASSWORD=admin888
      - LOG_LEVEL=debug
    ports:
      - 16686:16686
    networks:
      - jaeger
networks:
  jaeger:
````





