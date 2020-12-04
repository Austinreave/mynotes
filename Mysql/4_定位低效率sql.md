#### 1 查看SQL执行频率

MySQL 客户端连接成功后，通过 show [session|global] status 命令可以提供服务器状态信息。show [session|global] status 可以根据需要加上参数“session”或者“global”来显示 session 级（当前连接）的计结果和 global 级（自数据库上次启动至今）的统计结果。如果不写，默认使用参数是“session”。

下面的命令显示了当前 session 中所有统计参数的值：

```
show status like 'Com_______';
```

![1552487172501](C:/Users/星星/Desktop/mysql/2/文档/assets/1552487172501.png)  

```
show status like 'Innodb_rows_%';
```

![1552487245859](C:/Users/星星/Desktop/mysql/2/文档/assets/1552487245859.png)

#### 2 定位低效率执行SQL

- 慢查询日志 : 通过慢查询日志定位那些执行效率较低的 SQL 语句。
- show processlist  : 实时地查看 SQL 的执行情况。

#### 3 explain分析执行计划

通过以上步骤查询到效率低的 SQL 语句后，可以通过 EXPLAIN或者 DESC命令获取 MySQL如何执行 SELECT 语句的信息，包括在 SELECT 语句执行过程中表如何连接和连接的顺序。

查询SQL语句的执行计划 ： 

```sql
explain  select * from tb_item where id = 1;
```

![1552487489859](C:/Users/星星/Desktop/mysql/2/文档/assets/1552487489859.png)  

| 字段          | 含义                                                         |
| ------------- | ------------------------------------------------------------ |
| id            | 一条sql语句中执行的sql语句的顺序，id值越大，优先级越高，越先被执行 |
| select_type   | 表示 SELECT 的类型，查看哪些有没有使用嵌套查询的sql,SIMPLE说明最规范的sql |
| table         | 输出结果集的表                                               |
| type          | 表示表的连接类型，性能由好到差的连接类型为( system  --->  const  ----->  eq_ref  ------>  ref  ------->  ref_or_null---->  index_merge  --->  index_subquery  ----->  range  ----->  index  ------> all ) |
| possible_keys | 表示查询时，可能使用的索引                                   |
| key           | 表示实际使用的索引                                           |
| key_len       | 索引字段的长度                                               |
| rows          | 扫描行的数量                                                 |
| extra         | 执行情况的说明和描述                                         |

##### 3.1explain 之 id

 id 不同id值越大，优先级越高，越先被执行。 

``` SQL
EXPLAIN SELECT * FROM t_role WHERE id = (SELECT role_id FROM user_role WHERE user_id = (SELECT id FROM t_user WHERE username = 'stu1'))
```

![1556103009534](C:/Users/星星/Desktop/mysql/2/文档/assets/1556103009534.png) 

##### 3.2 explain 之 select_type

 表示 SELECT 的类型，常见的取值，如下表所示：

| select_type | 含义                                                         |
| ----------- | ------------------------------------------------------------ |
| SIMPLE      | 简单的select查询，查询中不包含子查询                         |
| PRIMARY     | 从一个嵌套查询的结果集里，查找索引的值                       |
| SUBQUERY    | 在SELECT 或 WHERE 列表中包含了子查询                         |
| DERIVED     | 在FROM 列表中包含的子查询，被标记为 DERIVED（衍生） MYSQL会递归执行这些子查询，把结果放在临时表中 |

##### 3.3 explain 之 table

展示这一行的数据是关于哪一张表的 

##### 3.4 explain 之 type

type 显示的是访问类型，是较为重要的一个指标，可取值为： 

| type   | 含义                                                         |
| ------ | ------------------------------------------------------------ |
| NULL   | MySQL不访问任何表，索引，直接返回结果， 比如 ·EXPLAIN  SELECT NOW(); |
| system | 表只有一行记录(等于系统表)，这是const类型的特例，一般不会出现 |
| const  | 主键或唯一索引扫描记录只有一条，单表唯一索引查询             |
| eq_ref | 主键或唯一索引扫描记录只有一条，关联唯一索引查询             |
| ref    | 非唯一性索引扫描，记录有多条，没有索引的查询                 |
| range  | 只检索给定返回的行，使用一个索引来选择行。 where 之后出现 between ， < , > , in 等操作。 |
| index  | index 与 ALL的区别为  index 类型只是遍历了索引树， 通常比ALL 快， ALL 是遍历数据文件。 SELECT id  FROM user |
| all    | 将遍历全表以找到匹配的行 SELECT * FROM user                  |

一般来说， 我们需要保证查询至少达到 range 级别， 最好达到ref ，结果值从最好到最坏以此是：

```
null>system > const > eq_ref > ref > range > index > all
```

##### 3.5 explain 之  key

+ possible_keys : 当前sql可能应用在这张表的索引， 一个或多个。 
+ key ： 当前sql实际使用的索引， 如果为NULL， 则没有使用索引。
+ key_len : 表示索引中使用的字节数， 该值为索引字段最大可能长度，并非实际使用长度，在不损失精确性的前提下， 长度越短越好 

##### 3.6 explain 之 rows

扫描行的数量，行数越少越好

##### 3.7explain 之 extra

其他的额外的执行计划信息，在该列展示 。

| extra            | 含义                                                         |
| ---------------- | ------------------------------------------------------------ |
| using  filesort  | 常见于 order by 和 group by对应的字段不是索引； 效率低       |
| using  temporary | 常见于 order by 和 group by对应的字段不是索引； 效率低       |
| using  index     | 表示相应的select操作使用了覆盖索引， 避免访问表的数据行， 效率不错。 |

#### 4 show profile分析SQL

通过profile，我们能够更清楚地了解SQL执行的过程。首先，我们可以执行一系列的操作，如下图所示：

```sql
show databases;

use db01;

show tables;

select * from tb_item where id < 5;

select count(*) from tb_item;
```

执行完上述命令之后，再执行show profiles 指令， 来查看SQL语句执行的耗时：

![1552489017940](C:/Users/星星/Desktop/mysql/2/文档/assets/1552489017940.png)  

通过show  profile for  query  query_id 语句可以查看到该SQL执行过程中每个线程的状态和消耗的时间：

![1552489053763](C:/Users/星星/Desktop/mysql/2/文档/assets/1552489053763.png) 

```tex
TIP ：
	Sending data 状态表示MySQL线程开始访问数据行并把结果返回给客户端，而不仅仅是返回个客户端。由于在Sending data状态下，MySQL线程往往需要做大量的磁盘读取操作，所以经常是整各查询中耗时最长的状态。
```
