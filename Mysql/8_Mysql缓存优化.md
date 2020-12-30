#### 1 概述

开启Mysql的查询缓存，当执行完全相同的SQL语句的时候，服务器就会直接从缓存中读取结果，当数据被修改，之前的缓存会失效，修改比较频繁的表不适合做查询缓存。

#### 1.2 操作流程

1. 客户端发送一条查询给服务器；
2. 服务器先会检查查询缓存，如果命中了缓存，则立即返回存储在缓存中的结果。否则进入下一阶段；
3. 服务器端进行SQL解析、预处理，再由优化器生成对应的执行计划；
4. MySQL根据优化器生成的执行计划，调用存储引擎的API来执行查询；
5. 将结果返回给客户端。

#### 1.3 查询缓存配置

1. 查看当前的MySQL数据库是否支持查询缓存：

   ```SQL
   SHOW VARIABLES LIKE 'have_query_cache';	
   ```

   ![1555249929012](./img/1555249929012.png) 

2. 查看当前MySQL是否开启了查询缓存 ：

   ```SQL
   SHOW VARIABLES LIKE 'query_cache_type';
   ```

   ![1555250015377](./img/1555250015377.png) 

3. 查看查询缓存的占用大小 ：

   ```SQL
   SHOW VARIABLES LIKE 'query_cache_size';
   ```

   ![1555250142451](./img/1555250142451.png)  	

4. 查看查询缓存的状态变量：

   ```SQL
   SHOW STATUS LIKE 'Qcache%';
   ```

   ![1555250443958](./img/1555250443958.png) 

   各个变量的含义如下：

   | 参数                 | 含义                                   |
   | -------------------- | -------------------------------------- |
   | Qcache_free_blocks   | 查询缓存中的可用内存块数               |
   | Qcache_free_memory   | 查询缓存的可用内存量                   |
   | Qcache_hits          | 查询缓存命中数                         |
   | Qcache_inserts       | 添加到查询缓存的查询数                 |
   | Qcache_lowmen_prunes | 由于内存不足而从查询缓存中删除的查询数 |
   | Qcache_not_cached    | 非缓存查询的数量                       |
   | Qcache_total_blocks  | 查询缓存中的块总数                     |

#### 1.5 查询缓存SELECT选项

可以在SELECT语句中指定两个与查询缓存相关的选项 ：

SQL_CACHE : 如果查询结果是可缓存的，并且 query_cache_type 系统变量的值为ON或 DEMAND ，则缓存查询结果 。

SQL_NO_CACHE : 服务器不使用查询缓存。它既不检查查询缓存，也不检查结果是否已缓存，也不缓存查询结果。

例子：

```SQL
SELECT SQL_CACHE id, name FROM customer;
SELECT SQL_NO_CACHE id, name FROM customer;
```



#### 2.6 查询缓存失效的情况（整个sql语句发生改动时）

1） SQL 语句不一致的情况， 要想命中查询缓存，查询的SQL语句必须一致。

```SQL
SQL1 : select count(*) from tb_item;
SQL2 : Select count(*) from tb_item;
```

2） 当查询语句中有一些不确定的时，则不会缓存。如 ： now() , current_date() 

```SQL
SQL1 : select * from tb_item where updatetime < now() limit 1;
SQL2 : select user();
SQL3 : select database();
```

3） 不使用任何表查询语句。

```SQL
select 'A';
```

4）  查询 mysql， information_schema数据库中的表时，不会走查询缓存。

```SQL
select * from information_schema.engines;
```

5） 在存储的函数，触发器或事件的主体内执行的查询。

6） 如果表更改，则使用该表的所有高速缓存查询都将变为无效并从高速缓存中删除

