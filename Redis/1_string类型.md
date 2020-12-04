#### 业务场景

1. 分表操作怎样保证ID的唯一性
   1. Oracle 数据库具有 sequence 设定
   2. 雪花算法通过机器生成
   3. 每次生成ID时，通过Redis自增生成后拿来用

   ```
   #设置数值数据增加指定范围的值
   incr key
   incrby key increment
   incrbyfloat key increment
   #设置数值数据减少指定范围的值
   decr key
   decrby key increment
   ```

2. 如何限制每个用户每天只能投票一次

   1. redis 控制数据的生命周期，通过数据是否失效控制业务行为，适用于所有具有时效性限定控制的操作

   ```
   设置数据具有指定的生命周期
   setex key seconds value //秒
   psetex key milliseconds value //毫秒
   ```

3. 主页高频访问信息显示控制，新浪微博大浏览量（大V）主页显示粉丝数与微博数量

   ```
   #1.在redis中为大V用户设定用户信息，以用户主键和属性值作为key
   eg: user:id:3506728370:fans → 12210947
   eg: user:id:3506728370:blogs → 6164
   eg: user:id:3506728370:focuss → 83
   
   #2.在redis中以json格式存储大V用户信息
   eg: user:id:3506728370 → {"id":3506728370,"name":"春晚","fans":12210862,"blogs":6164, "focus":83}
   ```

####  注意事项

1. **单数据操作与多数据操作的区别（节省开销）**

![image-20200805212559394](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200805212559394.png)

2. **string 作为数值操作**
   + string在redis内部存储默认就是一个字符串，当遇到增减类操作incr，decr时会转成数值型进行计算。
   + redis所有的操作都是原子性的，采用单线程处理所有业务，命令是一个一个执行的，因此无需考虑并发带来的数据影响。
   + 注意：按数值进行操作的数据，如果原始数据不能转成数值，或超越了redis 数值上限范围（9223372036854775807），将报错。

3. **数据库中的热点数据key命名惯例**

   ![image-20200805215154736](C:\Users\星星\AppData\Roaming\Typora\typora-user-images\image-20200805215154736.png)