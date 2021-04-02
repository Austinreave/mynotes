### 什么是数据库驱动

数据库驱动是不同数据库开发商（比如oracle mysql等）为了某一种开发语言环境（比如Go）能够实现统一的数据库调用而开发的一个程序，他的作用相当于一个翻译人员，将Go语言中对数据库的调用语言通过这个翻译，翻译成各个种类的数据库自己的数据库语言，当然这个翻译（数据库驱动）是由各个开发商针对统一的接口自定义开发的

### 下载并导入数据库驱动包

官方不提供实现，先下载第三方的实现，然后按照里面的说明下载驱动包：

```
$ go get github.com/go-sql-driver/mysql
```

最后导入包即可：

```
import "database/sql"
import _ "github.com/go-sql-driver/mysql"
```

### 连接至数据库

```
db, err := sql.Open("mysql", "root:root@/uestcbook")
```

### 执行操作

##### 预处理防止sql注入

```
stmt, err := db.Prepare("INSERT INTO users (name, age) VALUES (?, ?)")
res, err := stmt.Exec("gopher",27)
```

##### 直接执行

```
result, err := db.Exec("INSERT INTO users (name, age) VALUES (?, ?)","gopher",27)
```

##### 事务处理执行

```
tx, _ := db.Begin()
stmt, err := tx.Prepare("INSERT INTO users (name, age) VALUES (?, ?)")
defer stmt.Close()
if err != {
	tx.Rollback()
}
res, err := stmt.Exec("gopher",27)
if err != {
	tx.Rollback()
}
tx.Commit()
```

#### 查询操作

##### 单条查询

```
type MovieInfo struct {
    Id                   int64
    Movie_name           string
    Movie_director       string
}
var Info MovieInfo
sql := "select id,movie_name,movie_director from movie_info where id = ?"
err := db.QueryRow(sql,movie_id).Scan(&Info.Id, &Info.Movie_name, &Info.Movie_director)
```

##### 查询多条

```
type MovieInfo struct {
    Id                   int64
    Movie_name           string
    Movie_director       string
}
item := MovieInfo{}
list := []MovieInfo{}

sql := "select id,movie_name,movie_director from movie_info"
rows,err := db.Query(sql)

for rows.Next(){
  var mid int64
  var movie_name, movie_director string
  err = rows.Scan(&mid,&movie_name,&movie_director)
  if err != nil {
  		panic(err.Error())
  }
  item.Id = mid
  item.Movie_name = movie_name
  item.Movie_director = movie_director
  list = append(list,item)
}

```

### 各种方式效率分析

##### 写操作测试

```
    //方式1 insert
    //strconv,int转string:strconv.Itoa(i)
    start := time.Now()
    for i := 1001;i<=1100;i++{
        //每次循环内部都会去连接池获取一个新的连接，效率低下
        db.Exec("INSERT INTO user(uid,username,age) values(?,?,?)",i,"user"+strconv.Itoa(i),i-1000)
    }
    end := time.Now()
    fmt.Println("方式1 insert total time:",end.Sub(start).Seconds())
    
    //方式2 insert
    start = time.Now()
    for i := 1101;i<=1200;i++{
        //Prepare函数每次循环内部都会去连接池获取一个新的连接，效率低下
        stm,_ := db.Prepare("INSERT INTO user(uid,username,age) values(?,?,?)")
        stm.Exec(i,"user"+strconv.Itoa(i),i-1000)
        stm.Close()
    }
    end = time.Now()
    fmt.Println("方式2 insert total time:",end.Sub(start).Seconds())
    
    //方式3 insert
    start = time.Now()
    stm,_ := db.Prepare("INSERT INTO user(uid,username,age) values(?,?,?)")
    for i := 1201;i<=1300;i++{
        //Exec内部并没有去获取连接，为什么效率还是低呢？
        stm.Exec(i,"user"+strconv.Itoa(i),i-1000)
    }
    stm.Close()
    end = time.Now()
    fmt.Println("方式3 insert total time:",end.Sub(start).Seconds())
    
    //方式4 insert 
    start = time.Now()
    //Begin函数内部会去获取连接
    tx,_ := db.Begin()
    for i := 1301;i<=1400;i++{
        //每次循环用的都是tx内部的连接，没有新建连接，效率高
        tx.Exec("INSERT INTO user(uid,username,age) values(?,?,?)",i,"user"+strconv.Itoa(i),i-1000)
    }
    //最后释放tx内部的连接
    tx.Commit()
    
    end = time.Now()
    fmt.Println("方式4 insert total time:",end.Sub(start).Seconds())
    
    //方式5 insert
    start = time.Now()
    for i := 1401;i<=1500;i++{
        //Begin函数每次循环内部都会去连接池获取一个新的连接，效率低下
        tx,_ := db.Begin()
        tx.Exec("INSERT INTO user(uid,username,age) values(?,?,?)",i,"user"+strconv.Itoa(i),i-1000)
        //Commit执行后连接也释放了
        tx.Commit()
    }
    end = time.Now()
    fmt.Println("方式5 insert total time:",end.Sub(start).Seconds())
}
```

```
方式1 insert total time: 3.7952171
方式2 insert total time: 4.3162468
方式3 insert total time: 4.3392482
方式4 insert total time: 0.3970227
方式5 insert total time: 7.3894226
```

##### 读操作测试

```
    //方式1 query
    start := time.Now()
    rows,_ := db.Query("SELECT uid,username FROM USER")
    defer rows.Close()
    for rows.Next(){
         var name string
         var id int
        if err := rows.Scan(&id,&name); err != nil {
            log.Fatal(err)
        }
        //fmt.Printf("name:%s ,id:is %d\n", name, id)
    }
    end := time.Now()
    fmt.Println("方式1 query total time:",end.Sub(start).Seconds())
    
    
    //方式2 query
    start = time.Now()
    stm,_ := db.Prepare("SELECT uid,username FROM USER")
    defer stm.Close()
    rows,_ = stm.Query()
    defer rows.Close()
    for rows.Next(){
         var name string
         var id int
        if err := rows.Scan(&id,&name); err != nil {
            log.Fatal(err)
        }
       // fmt.Printf("name:%s ,id:is %d\n", name, id)
    }
    end = time.Now()
    fmt.Println("方式2 query total time:",end.Sub(start).Seconds())
    
    
    //方式3 query
    start = time.Now()
    tx,_ := db.Begin()
    defer tx.Commit()
    rows,_ = tx.Query("SELECT uid,username FROM USER")
    defer rows.Close()
    for rows.Next(){
         var name string
         var id int
        if err := rows.Scan(&id,&name); err != nil {
            log.Fatal(err)
        }
        //fmt.Printf("name:%s ,id:is %d\n", name, id)
    }
    end = time.Now()
    fmt.Println("方式3 query total time:",end.Sub(start).Seconds())
```

```
方式1 query total time: 0.0070004
方式2 query total time: 0.0100006
方式3 query total time: 0.0100006
```

### 深入内部分析原因分析

##### (1) sql.Open("mysql", "username:pwd@/databasename")

功能：返回一个DB对象，DB对象对于多个goroutines并发使用是安全的，DB对象内部封装了连接池。

实现：open函数并没有创建连接，它只是验证参数是否合法。然后开启一个单独goroutines去监听是否需要建立新的连接，当有请求建立新连接时就创建新连接。

注意：open函数应该被调用一次，通常是没必要close的。

##### (2) DB.Exec()

功能：执行不返回行（row）的查询，比如INSERT，UPDATE，DELETE

实现：DB交给内部的exec方法负责查询。exec会首先调用DB内部的conn方法从连接池里面获得一个连接。然后检查内部的driver.Conn实现了Execer接口没有，如果实现了该接口，会调用Execer接口的Exec方法执行查询；否则调用Conn接口的Prepare方法负责查询。

##### (3)DB.Query()

功能：用于检索（retrieval），比如SELECT

实现：DB交给内部的query方法负责查询。query首先调用DB内部的conn方法从连接池里面获得一个连接，然后调用内部的queryConn方法负责查询。

##### （4）DB.QueryRow()

功能：用于返回单行的查询

实现：转交给DB.Query()查询

##### (5）db.Prepare()

功能：返回一个Stmt。Stmt对象可以执行Exec,Query,QueryRow等操作。

实现：DB交给内部的prepare方法负责查询。prepare首先调用DB内部的conn方法从连接池里面获得一个连接，然后调用driverConn的prepareLocked方法负责查询。

Stmt相关方法：st.Exec()；st.Query()；st.QueryRow()；st.Close()

##### （6）db.Begin()

功能：开启事务，返回Tx对象。调用该方法后，这个TX就和指定的连接绑定在一起了。一旦事务提交或者回滚，该事务绑定的连接就还给DB的连接池。

实现：DB交给内部的begin方法负责处理。begin首先调用DB内部的conn方法从连接池里面获得一个连接，然后调用Conn接口的Begin方法获得一个TX。
