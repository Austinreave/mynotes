### bytes.buffer是什么

+ Buffer 是 bytes 包中的一个 type Buffer struct{} （其实底层就是一个 []byte， 字节切片）

+ bytes.buffer是一个缓冲byte类型的缓冲器存放着都是byte

+ Buffer 就像一个集装箱容器，可以存东西，取东西（存取数据）

### 创建 Buffer缓冲器

```
//第一种
var b bytes.Buffer //直接定义一个 Buffer 变量，而不用初始化
//第二种
b1 := new(bytes.Buffer) //直接使用 new 初始化，可以直接使用
//第三种
func NewBuffer(buf []byte) *Buffer //以函数的形式调用 参数为[]byte
//第四种
func NewBufferString(s string) *Buffer // 以函数的形式调用 参数为string
```

### 向 Buffer 中写入数据

##### 直接初始化写数据

```
buf1 := bytes.NewBufferString("swift") 
buf2 := bytes.NewBuffer([]byte("swift"))
buf3 := bytes.NewBuffer([]byte{'s', 'w', 'i', 'f', 't'})
```

##### 间接写数据

```
var b bytes.Buffer //直接定义一个 Buffer 变量，而不用初始化

b.Write(d []byte) //将切片d写入Buffer尾部

b.WriteString(s string) //将字符串s写入Buffer尾部

b.WriteByte(c byte) //将字符c写入Buffer尾部

b.WriteRune(r rune) //将一个rune类型的数据放到缓冲器的尾部
```

### 从 Buffer 中读取数据

```
//从第n个字节读取数据并返回
b.Next(n int) []byte

//读取第一个byte并返回，off 向后偏移 n
b.ReadByte() (byte, error)

//读取第一个 UTF8 编码的字符并返回该字符和该字符的字节数
b.ReadRune() (r rune, size int, err error)

//读取缓冲区第一个分隔符前面的内容以及分隔符并返回，缓冲区会清空读取的内容。如果没有发现分隔符，则返回读取的内容并返回错误io.EOF，delimiter指的是英文字符对应的ASCII码字符对应的数字
b.ReadBytes(delimiter byte) (line []byte, err error)

//读取缓冲区第一个分隔符前面的内容以及分隔符并作为字符串返回，缓冲区会清空读取的内容。如果没有发现分隔符，则返回读取的内容并返回错误 io.EOF
b.ReadString(delimiter byte) (line string, err error)

//将 Buffer 中的内容输出到实现了 io.Writer 接口的可写入对象中，成功返回写入的字节数，失败返回错误
b.WriteTo(w io.Writer) (n int64, err error)
```