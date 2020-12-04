#### 为什么要持久化

+ 希望容器之间能共享数据。
+ 当删除容器后，数据不丢失。

#### 1、使用bind mount

**能够自定义文件存在位置**

```bash
docker run -it -v $(pwd)/my-volume:/container-data php
```

有几点需要注意：

- host机器的目录路径必须为全路径(准确的说需要以`/`或`~/`开始的路径)，不然docker会将其当做volume处理
- 如果host机器上的目录不存在，docker会自动创建该目录
- 如果container中的目录不存在，docker会自动创建该目录
- 如果container中的目录已经有内容，那么docker会使用host上的目录（哪怕是没有内容）将其覆盖掉
- bind mount一般不出现在Dockerfile中，因为自定义目录可能不存在在其他宿主机上

#### 2、使用volume

**docker下所有的volume都在host机器上的指定目录下/var/lib/docker/volumes。**

```undefined
docker run -it user/my-volume:/container-data php
```

然后可以查看到给my-volume的volume：

```csharp
docker volume inspect my-volume
[
    {
        "CreatedAt": "2018-03-28T14:52:49Z",
        "Driver": "local",
        "Labels": null,
        "Mountpoint": "/var/lib/docker/volumes/user/my-volume/_data",
        "Name": "my-volume",
        "Options": {},
        "Scope": "local"
    }
]
```

**也可以不指定host上的volume：**

```undefined
docker run -it -v /mydata php
```

此时docker将自动创建一个匿名的volume，并将其挂载到container中的/mydata目录。匿名volume在host机器上的目录路径类似于：`/var/lib/docker/volumes/300c2264cd0acfe862507eedf156eb61c197720f69e/_data`。

**除了让docker帮我们自动创建volume，我们也可以自行创建：**

```undefined
docker volume create my-volume-2
```

然后将这个已有的my-volume-2挂载到container中:

```ruby
docker run -it -v my-volume-2:/mydata alpine sh
```

有几点需要注意：

+ 如果volume是空的而container中的目录有内容，那么docker会将container目录中的内容拷贝到volume中（宿主无数据，从容器复制过来，再以宿主机为准）
+ 如果volume中已经有内容，则会将container中的目录覆盖（宿主有数据时，以宿主机为准）。