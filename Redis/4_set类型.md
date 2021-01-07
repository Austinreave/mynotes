#### 业务场景

+ **用set给用户推送喜欢的东西**
  用户首次登陆网站，让他随机选喜欢的分类，比如选择了NBA和娱乐两个分类，后期给用户推送分类其相关内容
  
  ```
  添加
    SADD user_id001 科比扣篮绝杀
    SADD user_id001 姚明进入NBA
    SADD user_id001 姚笛文章约会 [其他热门]
  展示
  	SMEMBERS user_id001
  推送一条（不删除）
  	SRANDMEMBER user_id001 1
  推送一条（删除）
	SPOP user_id001 1
  ```
  
+ **用set处理某一个公众号关注的好友人数，比如自己微信里面关注公众号后会显示关注此公众号的好友人数**

   ```
   1、比如u001用户有 u002、u003两个好友（好友集合）
   	SADD u001 u001 u002 u003
   2、u001关注了公众号g001（公众号集合）
   	SADD g001 u001
   3、u002关注了公众号g001、g003（公众号集合）
   	SADD g001 u002
   	SADD g003 u002
   4、u003关注了公众号g001、g002（公众号集合）
   	SADD g001 u003
   	SADD g002 u003
   5、那么u001看到g001公众号好友关注人数时（两个集合的交集）
   	sinter u001 g001  (得到u001 u002 u003三个人数)
   ```

+ **用set实现一个权限管理功能**

   ```
   1、角色ri001 有getAll getById 两个接口调用权限
   	SADD ri001 getAll sgetById
   2、角色ri002 有getCount insert update
   	SADD ri002 getCount insert update
   3、u001有 ri001和ri002两就角色
   	sunionstore u001 ri001 ri002 ...（多个角色一块合,并集）
   4、获取u001所有权限
     smembers u001 （程序进行判断，推荐）
     sismbers u001 insert (redis判断，不推荐)
   ```

+ **用set实现一个网站每日的PV、UV、IP访问数量**

   ```
   PV：利用 string 类型的 INCR PV 
   UV和IP ： 
   	SADD IPS 1.2.3.4 
   	SCARD IPS  #获取集合的成员数
   ```

+ **用set实现一个黑名单或者白名单，当有IP访问时优先查询黑名单或者白名单进行校验**

   ```
   SADD hei 1.2.3.4
   SADD bai 1.2.3.4
   ```

#### set 类型数据操作的注意事项

+ set 类型不允许数据重复，如果添加的数据在 set 中已经存在，将只保留一份
+ set 虽然与hash的存储结构相同，但是无法启用hash中存储值的空间