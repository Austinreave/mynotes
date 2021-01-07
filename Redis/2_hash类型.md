#### 业务场景

+ 电商网站购物车设计与实现

  操作流程

  ```
  结构：
  id:user_id : {
  	goods_id_nums1:1
  	goods_id_info1:{}
  	goods_id_nums2:1
  	goods_id_info2:{}
  }
  
  示例：
  99:user_id : {
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
  ```

  ![image-20200806222654119](img\image-20200806222654119.png)

  

+ 双11活动日，销售手机充值卡的商家对移动、联通、电信的30元、50元、100元商品推出抢购活动，每种商品抢购上限1000张

  操作流程

  ![image-20200806223543094](img\image-20200806223543094.png)

  ![image-20200806223456532](img\image-20200806223456532.png)

#### hash 类型数据操作的注意事项

1. hash类型下的value只能存储字符串，不允许存储其他数据类型，不存在嵌套现象。如果数据未获取到，对应的值为（nil） 

2. 每个 hash 可以存储 2 32 - 1 个键值对

3. hash类型十分贴近对象的数据存储形式，并且可以灵活添加删除对象属性。但hash设计初衷不是为了存储大量对象而设计的，切记不可滥用，更不可以将hash作为对象列表使用

4. hgetall 操作可以获取全部属性，如果内部field过多，遍历整体数据效率就很会低，有可能成为数据访问瓶颈
5. string存储对象（json）与hash存储对象的区别就是hash可以操作，string适合读