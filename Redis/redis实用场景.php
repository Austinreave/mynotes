 hash使用场景
	 
	 存储购物车
	 	结构：
		user_id : {
			goods_id_nums1:1
			goods_id_info1:{}
			goods_id_nums2:1    
			goods_id_info2:{}
		}	
		
		示例：
		99：user_id :{
			101_nums:1
			101_info:"{"goods_name"："电脑","goods_img":"商品图片"}"
			201_nums:1
			201_info:"{"goods_name"："手机","goods_img":"商品图片"}"
		}

		获取购物车信息
			hget 99:user_id

		添加购物车信息

			先获取购物车中是否存在此商品。如果存在数量怎增加
				hget 99:user_id 101_nums
				HINCRBY 99:user_id 101_nums 1

			如果不存在进行添加
				hest 99：user_id :{
					301_nums:1
					301_info:"{"goods_name"："空调","goods_img":"商品图片"}"
				}

		修改购物车信息
				hset 99：user_id :{
					101_nums:8
					101_info:"{"goods_name"："电动车","goods_img":"商品图片"}"
				}

		删除购物车信息
				hdel 99：user_id 101_nums

		获取购物车总数
				hlen 99:user_id

		获取购物车所有信息
				hgetall  99:user_id


sort set 实现排行榜
	1、zadd fans 999 科比 888 詹姆斯 777 杜兰特 666 库里 （向有序集合添加一个或多个成员，或者更新已存在成员的分数）

	2、zrange fans 0 -1 //加上WITHSCORES时会展示对应的分数，否则只展示成员，从小到大

	3、zrevrange fans 0 -1  //加上WITHSCORES时会展示对应的分数，否则只展示成员，从大到小

	4、zrem fans 库里 //删除
	//成员条件
	5、zrangebyscore fans min max [WITHSCORES] [LIMIT] 
	//分数条件
	6、zrevrangebyscore key max min [WITHSCORES]
	// 成员条件删除数据 start与stop用于限定查询范围，作用于索引，表示开始和结束索引
	7、zremrangebyrank fans start stop 
	//分数条件删除数据  
	8、zremrangebyscore key min max

	9、集合交操作
	zinterstore destination numkeys key [key ...]
	10、集合并操作
	zunionstore destination numkeys key [key ...]


	11、获取数据对应的索引（排名）
	zrank key member //顺序
	zrevrank key member //倒叙

	12、score值获取与修改
	zscore key member
	zincrby key increment member


