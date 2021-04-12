#### Docker和虚拟机的区别

##### 虚拟机的体系结构

+ **server** - 表示真实物理机电脑

+ **Host OS** - 真实电脑的操作系统，例如：Windows，Linux

+ **Hypervisor** - 虚拟机平台，模拟硬件，如VMWare，VirtualBox

+ **Guest OS** - 虚拟机平台上安装的操作系统，例如CentOS Linux

+ **App** - 虚拟机操作系统上的应用，例如nginx

  ![aHR0cHM6Ly93d3cucWlrZWd1LmNvbS93cC1jb250ZW50L3VwbG9hZHMvMjAxOS8wNS92aXJ0dWFsaXphdGlvbi0xLmpwZw](./images/aHR0cHM6Ly93d3cucWlrZWd1LmNvbS93cC1jb250ZW50L3VwbG9hZHMvMjAxOS8wNS92aXJ0dWFsaXphdGlvbi0xLmpwZw.jpeg)

##### Docker的体系结构

- **server** - 表示真实物理机电脑。
- **Host OS** - 真实电脑的操作系统，例如：Windows，Linux。
- **Docker Engine** - 新一代虚拟化技术，不需要包含单独的操作系统，使用的还是Host OS。
- **App** - 所有的应用程序现在都作为Docker容器运行。

![aHR0cHM6Ly93d3cucWlrZWd1LmNvbS93cC1jb250ZW50L3VwbG9hZHMvMjAxOS8wNS92YXJpb3VzX2xheWVycy0xLmpwZw](./images/aHR0cHM6Ly93d3cucWlrZWd1LmNvbS93cC1jb250ZW50L3VwbG9hZHMvMjAxOS8wNS92YXJpb3VzX2xheWVycy0xLmpwZw.jpeg)

##### docker优势

Docker不需要为虚拟机操作系统提供硬件模拟。所有应用程序都作为Docker容器工作，性能更好

#### Docker 资源隔离

+ ⼀个没有资源限制的容器，可以使⽤宿主机的所有资源，其实和直接部署在宿主机一个道理
+ Docker 在控制内存、CPU、I/O，引入了 Linux系统内核的机制

  + namespace
    1. 提供一种隔离机制，让不同的namespace下的进程看到的全局资源不同，每一个namespace有一个自己独立的全局资源实例。
    2. 提供Pid，Network，内存、CPU、I/O等资源的隔离，每个Namespace下的这些资源对于其他Namespace是不可见的。
  + cgroup
    1. 它提供了一套机制用于控制一组特定进程对资源的使用。
+  Docker 是借助linux内核的 namespace、cgroup 实现的资源隔离、 资源限额、包括⼀定的虚拟化技术，namespace使得容器像⼀台独⽴的计算机。
+ namespace实现容器间资源隔离，cgroup 实现限制容器使⽤内存、CPU、IO写⼊。

