##### go build

1. go build 只会编译main包下的main文件。我们知道go的入口文件是main包
2. go build 编译生成的二进制文件在当前目录
3. go build -o newpkg main.go  自定义输出的二进制文件名字

##### go install

1. go install 就是先编译后，再把二进制文件移动到特定目录
2. go install 的使用和 go build 大部分都是相似的，只是不能使用o参数自定义输出

##### go get

1. mod 开启时go get 的下载路径为 $GOPATH/pkg/mod，当 mod 关闭时go get 的下载路径为 $GOPATH/src。
2. go get -v  packages 查看安装进度
3. go get -d  packages 此命令仅仅引入远程的包，本地不下载。
4. go get -u  packages 就是口中常说的更新包

##### go run：

1. go run  先编译 main 包下的 main 函数，然后再运行$GOPATH/bin目录下的这个文件。

##### go mod：

1. mod 机制把所有依赖都放在 $GOPATH/pkg/mod 目录下，里面可以存放着不同版本的依赖。然后在每个项目中都有一个 mod 文件，在这里设置使用的依赖类型和版本。这样迁移项目和管理依赖都方便许多。
2. 首先打开 go mod 需要 设置 GO111MODUL 环境变量，不过最新现在已经默认开启。
3.  go mod 现在只支持 $GOPATH/src 目录外，从$GOPATH/pkg/mod 下寻找依赖。
4. go mod init  命令初始化建立 go.mod 文件，开启 mod 支持了
5. go mod tidy  进行下载依赖的包
6. go mod vendor 命令会在项目根目录新建 vendor 目录，会把依赖复制到这个目录。

##### go env：

1. GOROOT：用于存放 go 的安装路径，如标准库，工具链等就放在这个位置。
2. GOPATH：工作目录
3. GOBIN：在 go install 中说过，安装可执行程序时，会安装到 $GOPATH/bin 目录，这个目录就是 GOBIN
4. GOOS 和 GOARCH：go 本身支持交叉编译，也就是可以在一个平台上生成另一个平台的可执行程序。因此需要设置目标操作系统和目标系统架构
5. GOPROXY：使用 go mod 管理包拉取依赖时，通过修改 GOPROXY 变量即可修改 go mod 拉取的代理。
6. GO111MODULE：GO111MODULE 即 go1.11 版本推出的 go mod 机制。go mod 是最新的一种包管理工具。1.13 后便默认开启了





