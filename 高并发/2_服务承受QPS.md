##### 查询服务器最大QPS

- QPS最大值得获取和服务器CPU、内存、I/O有密切关联必须满足条件才能测出来。
- 系统CPU利用率：如果系统的CPU使用率已经很高，说明我们的系统是个计算度很复杂的系统，这时候如果QPS已经上不去了，就需要赶紧扩容，通过增加机器分担计算的方式来提高系统的吞吐量。
- 系统内存：如果CPU使用率一般，但是系统的QPS上不去，说明我们的机器并没有忙于计算，而是收到其他资源的限制，如内存、I/O。这时候首先看下内存是不是已经不够了，如果内存不够了，那就赶紧扩容了。

##### ab压测

安装ab工具

```
[root@localhost ~]$yum install -y httpd-tools
```

指定并发请求数为10，总请求数为1000，对http://www.91.cnm.com/进行压力测试

```
[root@localhost ~]$ab -c 10 -n 1000 http://www.91cnm.com/
```

```
This is ApacheBench, Version 2.3 <$Revision: 1430300 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/
 
Benchmarking www.91cnm.com (be patient)
Completed 100 requests    # 已经完成了100个请求
Completed 200 requests    # 已经完成了200个请求
Completed 300 requests    
Completed 400 requests    # 这个地方是关注点之一，如果全部完成了，说明服务器能扛住我们所指定的请求量
Completed 500 requests    # 我们可以继续增大请求数来进行测试，直到扛不住了就是最大的所能处理的请求了
Completed 600 requests    
Completed 700 requests    
Completed 800 requests    
Completed 900 requests    
Completed 1000 requests   
Finished 1000 requests    
                          
Server Software:        nginx            # 所请求的服务端软件
Server Hostname:        www.91cnm.com    # 所请求的服务端主机名
Server Port:            80               # 所请求的服务端端口
 
Document Path:          /                # 请求的URL资源
Document Length:        10507 bytes      # 请求的页面大小
 
Concurrency Level:      10               # 并发请求数，也就是我们用 -c 10 指定的数量
Time taken for tests:   43.339 seconds   # 总访问时间，也就是服务器处理完这些请求所花费的时间
Complete requests:      1000             # 请求成功的数量
Failed requests:        0                # 请求失败的数量，这个地方也是关注点之一，如果出现有失败的，说明有点扛不住了
Write errors:           0                # 网络连接写入错误数
Total transferred:      10645000 bytes                                        # 请求的总数据大小(包括header头信息)
HTML transferred:       10507000 bytes                                        # 请求的HTML文档的总数据大小
# 平均每秒请求数，是总请求数除以处理完成这些请求数所花费的时间的结果，也是重要指标之一
Requests per second:    53.07 [#/sec] (mean)                                 
Time per request:       433.387 [ms] (mean)                                   # 表示用户平均请求等待时间，参考：https://www.imooc.com/article/19952
Time per request:       43.339 [ms] (mean, across all concurrent requests)    # 表示服务器平均请求处理时间，参考：https://www.imooc.com/article/19952
Transfer rate:          239.87 [Kbytes/sec] received                          # 平均每秒传输多少K，也就是服务器的带宽了
 
Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:      150  164  32.4    168    1163
Processing:   150  265 307.8    168    2726
Waiting:      150  264 307.9    168    2726
Total:        300  429 311.4    336    2895
 
Percentage of the requests served within a certain time (ms)
  50%    336    # 50%的请求数在336ms内返回
  66%    338    # 66%的请求数在338ms内返回
  75%    340    
  80%    341    # 这个地方也是关注点之一，我们不仅要扛住这么多请求，而且要尽快地处理请求并响应回客户端
  90%    697    
  95%   1073    
  98%   1766    
  99%   1843    
 100%   2895 (longest request)
```

服务端性能监测 top ，此时cpu接近最高内存还有富余，说明QPS最大为50左右

<img src="img/20190517141629556.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L20wXzM4MDA0NjE5,size_16,color_FFFFFF,t_70" alt="img" style="zoom:50%;" />