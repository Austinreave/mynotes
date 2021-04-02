### Duck Typing的好处

类型 T 不需要显式地声明它实现了接口 I。**只要类型 T 实现了所有接口 I 规定的函数，它就自动地实现了接口 I**。 这样就像动态语言一样省了很多代码，少了许多限制。

### 实现过程

```
package main

import (
	"fmt"
)

//接口
type ISayHello interface {
	SayHello()
}

//结构体
type Person struct {}

//结构体方法同时实现了接口的方法
func (person Person) SayHello() {
	fmt.Println("Hello!")
}

//结构体
type Duck struct {}

//结构体方法同时实现了接口的方法
func (duck Duck) SayHello() {
	fmt.Println("ga ga ga!")
}

//形参为接口类型，此时发挥的功能则是传过来的i的功能
func greeting(i ISayHello) {
	i.SayHello()
}

func main () {

	//1、如果某个结构体person实现了某个接口ISayHello内的所有方法SayHello
	//2、那么将一个类型为Person的变量person赋值给一个类型为ISayHello的变量i时，那么这个变量i就赋予了结构体person的功能
	person := Person{}
	var i ISayHello
	i = person
	greeting(i)
	
	//如果某个结构体duck实现了某个接口ISayHello内的所有方法SayHello
	//那么这个结构体duck就实现该接口ISayHello
	duck := Duck{}
	greeting(duck)
}
```



### 使用实例

用 fmt.Fprintf 向一个 http 连接写入 http 响应：

```
func main () {
	http.HandleFunc("/",hello)
}

func hello(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "Hello")
}
```

Golang 的 fmt.Fprintf 函数的第一个参数的类型是一个 io.Writer 接口的接口变量。

```
type Writer interface {
    Write(p []byte) (n int, err error)
}
```

net/http 中的 http.ResponseWriter 是 http的 响应功能，它实现了 Write() 这个方法，因此，它自动实现了 Writer 这一接口。所以，我们在 http 的请求处理函数时，就可以直接用 Fprintf 来向一个 http.ResponseWriter 对象写入响应。