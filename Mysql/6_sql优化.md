+ **优化insert语句**

  如果需要同时对一张表插入很多行数据时，应该尽量使用多个值表的insert语句，这种方式将大大的缩减客户端与数据库之间的连接、关闭等消耗。使得效率比分开执行的单个insert语句快。

+ **优化order by语句**

   #####  环境准备

  ```SQL
  CREATE TABLE `emp` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `age` int(3) NOT NULL,
    `salary` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;
  create index idx_emp_age_salary on emp(age,salary);
  ```

  #####  两种排序方式

  1). 第一种是通过对返回数据进行排序，也就是通常说的 filesort 排序，所有不是通过索引直接返回排序结果的排序都叫 FileSort 排序。

  ![1556335817763](C:/Users/星星/Desktop/mysql/2/文档/assets/1556335817763.png) 

  2). 第二种通过有序索引顺序扫描直接返回有序数据，这种情况即为 using index，不需要额外排序，操作效率高。

  ![1556335866539](C:/Users/星星/Desktop/mysql/2/文档/assets/1556335866539.png) 

  多字段排序

  ![1556336352061](C:/Users/星星/Desktop/mysql/2/文档/assets/1556336352061.png) 

  

  了解了MySQL的排序方式，优化目标就清晰了：尽量减少额外的排序，通过索引直接返回有序数据。where 条件和Order by 使用相同的索引，并且Order By 的顺序和索引顺序相同， 并且Order  by 的字段都是升序，或者都是降序。否则肯定需要额外的操作，这样就会出现FileSort。

+ **优化group by 语句**

  由于GROUP BY 实际上也同样会进行排序操作，而且与ORDER BY 相比，GROUP BY 主要只是多了排序之后的分组操作。当然，如果在分组的时候还使用了其他的一些聚合函数，那么还需要一些聚合函数的计算。所以，在GROUP BY 的实现过程中，与 ORDER BY 一样也可以利用到索引。

  如果查询包含 group by 但是用户想要避免排序结果的消耗， 则可以执行order by null 禁止排序。如下 ：

  ```SQL
  drop index idx_emp_age_salary on emp;
  
  explain select age,count(*) from emp group by age;
  ```

  ![1556339573979](C:/Users/星星/Desktop/mysql/2/文档/assets/1556339573979.png)  

  优化后

  ```sql
  explain select age,count(*) from emp group by age order by null;
  ```

  ![1556339633161](C:/Users/星星/Desktop/mysql/2/文档/assets/1556339633161.png)  

  从上面的例子可以看出，第一个SQL语句需要进行"filesort"，而第二个SQL由于order  by  null 不需要进行 "filesort"， 而上文提过Filesort往往非常耗费时间。

  创建索引 ：

  ```SQL
  create index idx_emp_age_salary on emp(age,salary)；
  ```

  ![1556339688158](C:/Users/星星/Desktop/mysql/2/文档/assets/1556339688158.png) 

+ **优化嵌套查询**

  有些情况下，子查询是可以被更高效的连接（JOIN）替代。

  示例 ，查找有角色的所有的用户信息 : 

  ```SQL
   explain select * from t_user where id in (select user_id from user_role );
  ```

  执行计划为 : 

  ![1556359399199](C:/Users/星星/Desktop/mysql/2/文档/assets/1556359399199.png)   

  

  优化后 :

  ```SQL
  explain select * from t_user u , user_role ur where u.id = ur.user_id;
  ```

  ![1556359482142](C:/Users/星星/Desktop/mysql/2/文档/assets/1556359482142.png)   

  

  连接(Join)查询之所以更有效率一些 ，是因为MySQL不需要在内存中创建临时表来完成这个逻辑上需要两个步骤的查询工作。

+ 优化OR条件

  对于包含OR的查询子句，如果要利用索引，则OR之间的每个条件列都必须用到索引 ， 而且不能使用到复合索引； 如果没有索引，则应该考虑增加索引。

  获取 emp 表中的所有的索引 ： 

  ![1556354464657](C:/Users/星星/Desktop/mysql/2/文档/assets/1556354464657.png)  

  示例 ： 

  ```SQL
  explain select * from emp where id = 1 or age = 30;
  ```

  ![1556354887509](C:/Users/星星/Desktop/mysql/2/文档/assets/1556354887509.png)

  ![1556354920964](C:/Users/星星/Desktop/mysql/2/文档/assets/1556354920964.png)  

  建议使用 union 替换 or ： 

  ![1556355027728](C:/Users/星星/Desktop/mysql/2/文档/assets/1556355027728.png) 

  我们来比较下重要指标，发现主要差别是 type 和 ref 这两项

  type 显示的是访问类型，是较为重要的一个指标，结果值从好到坏依次是：

  ```
  system > const > eq_ref > ref > fulltext > ref_or_null  > index_merge > unique_subquery > index_subquery > range > index > ALL
  ```

  没有UNION 语句的 type 值为 ref，OR 语句的 type 值为 range，可以看到这是一个很明显的差距

  有UNION 语句的 ref 值为 const，OR 语句的 type 值为 null，const 表示是常量值引用，非常快

  这两项的差距就说明了 UNION 要优于 OR 。

+ 优化分页查询

  一般分页查询时，通过创建覆盖索引能够比较好地提高性能。一个常见又非常头疼的问题就是 limit 2000000,10  ，此时需要MySQL排序前2000010 记录，仅仅返回2000000 - 2000010 的记录，其他记录丢弃，查询排序的代价非常大 。

  ![1556361314783](C:/Users/星星/Desktop/mysql/2/文档/assets/1556361314783.png) 

  ##### 优化思路一

  在索引上完成排序分页操作，最后根据主键关联回原表查询所需要的其他列内容。

  ![1556416102800](C:/Users/星星/Desktop/mysql/2/文档/assets/1556416102800.png) 

  

  ##### 优化思路二

  该方案适用于主键自增的表，可以把Limit 查询转换成某个位置的查询 。

  ![1556363928151](C:/Users/星星/Desktop/mysql/2/文档/assets/1556363928151.png) 

+ **使用SQL提示**

  SQL提示，是优化数据库的一个重要手段，简单来说，就是在SQL语句中加入一些人为的提示来达到优化操作的目的。

  ##### USE INDEX

  在查询语句中表名的后面，添加 use index 来提供希望MySQL去参考的索引列表，就可以让MySQL不再考虑其他可用的索引。

  ```
  create index idx_seller_name on tb_seller(name);
  ```

  ![1556370971576](C:/Users/星星/Desktop/mysql/2/文档/assets/1556370971576.png) 

  ##### IGNORE INDEX

  不使用某个索引

  ```
   explain select * from tb_seller ignore index(idx_seller_name) where name = '小米科技';
  ```

  ![1556371004594](C:/Users/星星/Desktop/mysql/2/文档/assets/1556371004594.png) 

  ##### FORCE INDEX

  为强制MySQL使用一个特定的索引，可在查询中使用 force index 作为hint 。比如不让mysql自己判断用不用索引 

  ``` SQL
  create index idx_seller_address on tb_seller(address);
  ```

  ![1556371355788](C:/Users/星星/Desktop/mysql/2/文档/assets/1556371355788.png) 

  

  