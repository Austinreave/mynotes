#### 基本操作

**排行榜**

	1、zadd fans 999 科比 888 詹姆斯 777 杜兰特 666 库里 （向有序集合添加一个或多个成员，或者更新已存在成员的分数）
	2、zrange fans 0 -1 //加上WITHSCORES时会展示对应的分数，否则只展示成员，顺序
	3、zrevrange fans 0 -1  //加上WITHSCORES时会展示对应的分数，否则只展示成员，倒叙
	4、zrem fans 库里 //删除
![image-20200813215103045](img\image-20200813215103045.png)

	//成员条件查顺序
	5、zrangebyscore fans min max [WITHSCORES] [LIMIT] 
	//成员条件查倒叙
	6、zrevrangebyscore key max min [WITHSCORES]
	// 索引条件删除数据 start与stop用于限定查询范围，作用于索引，表示开始和结束索引
	7、zremrangebyrank fans start stop 
	//分数条件删除数据  
	8、zremrangebyscore key min max
![image-20200813215435404](img\image-20200813215435404.png)

<img src="img\image-20200813220154383.png" alt="image-20200813220154383" style="zoom: 80%;" />

	9、集合交操作
	zinterstore destination numkeys key [key ...]
	10、集合并操作
	zunionstore destination numkeys key [key ...]
![image-20200813220308041](img\image-20200813220308041.png)

![image-20200813220744026](img\image-20200813220744026.png)

	11、获取数据对应的索引（排名）
	zrank key member
	zrevrank key member
	
	12、score值获取与修改
	zscore key member //查看分数
	zincrby key increment member //分数增加
![image-20200813221207859](img\image-20200813221207859.png)

**sorted_set 类型数据操作的注意事项**

+ score保存的数据存储空间是64位，如果是整数范围是-9007199254740992~9007199254740992

+ score保存的数据也可以是一个双精度的double值，基于双精度浮点数的特征，可能会丢失精度，使用时候要慎重

+ sorted_set 底层存储还是基于set结构的，因此数据不能重复，如果重复添加相同的数据，score值将被反复覆盖，保留最后一次修改的结果