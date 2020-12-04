### goroutine调度模型

##### 协程特点

+ 有独立的栈空间（指向自己的函数）
+ 共享程序的堆空间（数据存储的地方）
+ 调度由用户控制
+ 协程是轻量级线程

##### MPG特点

+ 是用户态的自己来维护各个协程之间的调用

##### MPG介绍

+ **Machine** : 主线程或者进程main函数的入口函数
+ **Processor**：指承载多个goroutine的运行器，运行协程的上下文环境
+ **Goroutine** : 指应用里创建的goroutine

#### MPG 模式运行的状态 1

+ 一个Machine会对应一个内核线程（K），同时会有一个Processor与它绑定。一个Processor连接一个或者多个Goroutine。Processor有一个运行时的Goroutine（上图中绿色的G），其它的Goroutine处于等待状态。
+ Processor可通过GOMAXPROCS限制同时执行用户级任务的操作系统线程。GOMAXPROCS值默认是CPU的可用核心数，但是其数量是可以指定的。

+ ![image-20200704132207774](./img\image-20200704132207774.png)



#### MPG 模式运行的状态 2

![image-20200704132723107](.\img\image-20200704132723107.png)