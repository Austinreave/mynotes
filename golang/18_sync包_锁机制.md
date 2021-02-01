#### **临时对象池** (sync.Pool)

+ Pool是用于存储那些被分配了但是没有被使用，而未来可能会使用的值，以减小垃圾回收的压力。

+ Pool是协程安全的，应该用于管理协程共享的变量，不推荐用于非协程间的对象管理。

+ 代码示例：

  + ```
    func main() {
       var pool sync.Pool
       //put 将数据存储在临时对象池
       pool.Put(1)
       pool.Put("hello")
       pool.Put(true)
       pool.Put(3.14)
    
       //get 将数据从临时对象池取出
       value := pool.Get()
       fmt.Println(value)
       //临时对象池第一个数据在最前，后续的数据采用先进后出的原则
       value = pool.Get()
       fmt.Println(value)
       //需要函数类型变量
       pool.New = Demo
    }
    ```

 #### 等待组(sync.WaitGroup)

+ WaitGroup 用于等待一组协程的结束。

+ 主协程创建每个子协程的时候先调用Add增加等待计数，每个子协程在结束时调用Done减少协程计数。

+ 主协程通过 Wait 方法开始等待，直到计数器归零才继续执行。

+ 代码示例：

  + ```
    func main() {
       var m sync.Mutex
       wg := sync.WaitGroup{}
       count := 0
       //add 添加 done 完成（add(-1)）wait
       for i := 0; i < 100; i++ {
          wg.Add(1)
          go func() {
             m.Lock() //加锁
             defer wg.Done() //完成
             //数据处理
             count++
             m.Unlock() //解锁
          }()
       }
       wg.Wait()
       fmt.Println(count)
       fmt.Println("程序继续执行")
    }
    ```


#### 互斥锁 (sync.Mutex)

+ 互斥锁用来保证在任一时刻，只能有一个协程访问某对象。

+ Mutex的初始值为解锁状态，Mutex通常作为其它结构体的匿名字段使用，使该结构体具有Lock和Unlock方法。

+ Mutex可以安全的再多个协程中并行使用。

+ 如果对未加锁的进行解锁，则会引发panic。

+ 代码示例：

  + ```
    //不建议全局变量在协程中使用  如果使用 需要加锁
    var num = 0
    var wg sync.WaitGroup
    var mu sync.Mutex
    
    func Count() {
       mu.Lock()
       defer mu.Unlock()
       num++
       wg.Done()
    }
    func main() {
       wg.Add(10000)
       for i := 0; i < 10000; i++ {
          go Count()
       }
    
       wg.Wait()
    
       fmt.Println(num)
       //sync.RWMutex{}
    }
    ```

#### 读写锁(sync.RWMutex)

+ RWMutex比Mutex多了一个“写锁定” 和 “读锁定”，可以让多个协程同时读取某对象。

+ RWMutex可以安全的在多个协程中并行使用。

+ ```
  // Lock 将 rw 设置为写状态，禁止其他协程读取或写入
  func (rw *RWMutex) Lock()
  
  // Unlock 解除 rw 的写锁定状态，如果rw未被锁定，则该操作会引发 panic。
  func (rw *RWMutex) Unlock()
  
  // RLock 将 rw 设置为锁定状态，禁止其他协程写入，但可以读取。
  func (rw *RWMutex) RLock()
  
  // Runlock 解除 rw 设置为读锁定状态，如果rw未被锁定，则该操作会引发 panic。
  func (rw *RWMutex) RUnLock()
  ```

####  条件等待(sync.Cond)

+ 条件等待和互斥锁有不同，互斥锁是不同协程公用一个锁，条件等待是不同协程各用一个锁，但是wait()方法调用会等待（阻塞），直到有信号发过来，不同协程是共用信号。

+ cond.L.Lock() //加锁
  cond.L.Unlock() //解锁
  cond.Wait()	//等待接收信号才开始执行后面的代码
  cond.Signal() //发送信号

+ 代码示例

  + ```
    func main() {
       cond := sync.NewCond(new(sync.Mutex))
       for i := 0; i < 3; i++ {
          go func(i int) {
             wg.Add(1)
             defer wg.Done()
             fmt.Println("协程启动：", i)
             cond.L.Lock() //加锁
             fmt.Println("协程：", i, "加锁")
             cond.Wait() //等待接收信号才开始执行后面的代码
    			
             cond.L.Unlock() //解锁
             fmt.Println("协程：", i, "解锁")
    
          }(i)
       }
    
       time.Sleep(time.Second * 2)
       //发送信号
       cond.Signal()
       fmt.Println("主协程发送信号")
    
       time.Sleep(time.Second * 2)
       //发送信号
       cond.Signal()
       fmt.Println("主协程发送信号")
    
       time.Sleep(time.Second * 2)
       //发送信号
       cond.Signal()
       fmt.Println("主协程发送信号")
    }
    ```



#### 单次执行(sync.Once)

+ Once的作用是多次调用但只执行一次，Once只有一个方法，Once.Do()，向Do传入一个函数，这个函数在第一次执行Once.Do()的时候会被调用
+ 以后再执行Once.Do()将没有任何动作，即使传入了其他的函数，也不会被执行，如果要执行其它函数，需要重新创建一个Once对象。
+ Once可以安全的再多个协程中并行使用，是协程安全的。

+ 代码示例

  + ```
    func Test() {
    	fmt.Println("性感法师在线教学~")
    }
    func main() {
       var once sync.Once
       for i := 0; i < 10; i++ {
          //多次在协程调用  执行一次
          go func() {
             once.Do(Test) //函数回调
          }()
          //Test()
       }
    
       time.Sleep(time.Second * 2)
    }
    ```

#### 并发安全Map(sync.Map)

+ Go语言中的 map 在并发情况下，只读是线程安全的，同时读写是线程不安全的。需要并发读写时，一般的做法是加锁，但这样性能并不高，Go语言在 1.9 版本中提供了一种效率较高的并发安全的 sync.Map，sync.Map 和 map 不同，不是以语言原生形态提供，而是在 sync包下的特殊结构。
+ sync.Map 有以下特性：
  + 无须初始化，直接声明即可。
  + sync.Map 不能使用 map 的方式进行取值和设置等操作，而是使用 sync.Map 的方法进行调
    用，Store 表示存储，Load 表示获取，Delete 表示删除。
  + 使用 Range 配合一个回调函数进行遍历操作，通过回调函数返回内部遍历出来的值，Range
    参数中回调函数的返回值在需要继续迭代遍历时，返回 true，终止迭代遍历时，返回false。

+ 代码示例

  + ```
    func Print(key, value interface{}) bool {
       fmt.Println("键：", key, "值：", value)
       return true
    }
    func main() {
       var smap sync.Map
       //将键值对存储在sync.map
       smap.Store("法师", 33)
       smap.Store("兵哥", 18)
       smap.Store("木子", 22)
    
       //从map中获取数据
       //value, ok := smap.Load("老子")
       //if ok {
       // fmt.Println(value)
       //} else {
       // fmt.Println("未找到数据")
       //}
    
       //从map中删除数据
       smap.Delete("法师")
    
       //遍历数据(函数回调  将函数作为函数参数)
       smap.Range(Print)
    }
    ```
