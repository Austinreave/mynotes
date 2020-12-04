## 一、文件结构说明

```bash
│  esgo.md                                       教案
│  README.md                                     本文件
│  
└─code
    └─grpcdemo
        │  readme.txt
        │  
        ├─client
        │      main.go
        │      
        ├─es                                     ES导入数据
        │      main.go
        │      
        ├─esquery                                查询ES
        │      main.go
        │      
        ├─etcdservice
        │      naming.go
        │      resolver.go
        │      
        ├─models
               album_model.go
               article_model.go
               convertor.go
               esutils.go                        ES工具包
               home_model.go
               tags_model.go
               user_model.go

```



## 参考

opentracing 中文文档

https://wu-sheng.gitbooks.io/opentracing-io/content/

