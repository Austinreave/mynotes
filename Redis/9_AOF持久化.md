**AOF概念**

+ AOF(append only file)持久化：以独立日志的方式记录每次写命令，重启时再重新执行AOF文件中命令达到恢复数据的目的。与RDB相比可以简单描述为改记录数据为记录数据产生的过程

+ AOF的主要作用是解决了数据持久化的实时性，目前已经是Redis持久化的主流方式

**AOF写数据三种策略**

+ always：每次写入操作均同步到AOF文件中，数据零误差，性能较低（不建议使用）

+ everysec：每秒将缓冲区中的指令同步到AOF文件中，数据准确性较高，性能较高，在系统突然宕机的情况下丢失1秒内的数据（建议使用）

+ no：由操作系统控制每次同步到AOF文件的周期，整体过程不可控

**AOF功能开启**

+ 配置

  ```
  appendonly yes|no //是否开启AOF持久化功能，默认为不开启状态
  
  appendfsync always|everysec|no //AOF写数据策略  
  appendfilename filename//AOF持久化文件名，默认文件名未appendonly.aof，建议配置为appendonly-端口号.aof
  ```

+ ![image-20200819214833537](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819214833537.png)
+ ![image-20200819214902016](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819214902016.png)

**如果连续执行如下指令该如何处理**

![image-20200819215524960](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819215524960.png)

**AOF重写**

+ 随着命令不断写入AOF，文件会越来越大，为了解决这个问题，Redis引入了AOF重写机制压缩文件体积。简单说就是将对同一个数据的若干个条命令执行结果转化成最终结果数据对应的指令进行记录。

**AOF重写作用**

+ 降低磁盘占用量，提高磁盘利用率

+ 提高持久化效率，降低持久化写时间，提高IO性能

+ 降低数据恢复用时，提高数据恢复效率

**AOF重写方式**

+ 手动重写`bgrewriteaof`
+ 自动重写
  + ![image-20200819220308210](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819220308210.png)
  + ![image-20200819220355427](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819220355427.png) 

**AOF写数据过程**

+ ![image-20200819214417136](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819214417136.png)

+ 

![image-20200819214345159](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819214345159.png)

**RDB与AOF区别**

![image-20200819220505812](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200819220505812.png)