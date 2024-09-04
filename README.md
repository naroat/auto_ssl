### 功能

该脚本使用certbot生成证书分发到指定服务器, 实现一台机器管理证书, 可自动去阿里云(还包括腾讯云)创建dns记录完成验证，并自动部署ssl文件，只需设置一下配置文件即可

### 安装certbot

`https://certbot.eff.org/`

### 添加dns记录值 

这里使用到了第三方插件库，为了适应本项目对第三方插件库进行了适量的修改，并集成到本项目

> 添加dns记录值插件原始地址: https://github.com/ywdblog/certbot-letencrypt-wildcardcertificates-alydns-au

### 目录结构
```
dns-au/: 修改并集成的第三方库，功能是添加dns记录
conf.d/:         配置文件目录; 示例配置文件: *.example.com.conf
auto_ssl.sh:     脚本文件, 需要一个参数; 传配置文件名称
```

### 使用示例

命令：

```
# 1: 使用前拷贝配置模板例子, 将其中的参数换成自己的即可
cp  ./conf.d/*.example.com.conf ./conf.d/[你的域名].conf
# 2: 执行脚本, 只用传递配置名称
./auto_ssl.sh ./conf.d/[你的域名].conf   
```

自定义配置
```conf
###### 配置 ######
# 脚本环境(php,python)
SCRIPT_ENV="php"
# 域名: 以下配置申请泛域名和根域名ssl证书
DOMAIN_NAMES=("*.example.wang example.wang")
# dns类型(aly or txy)
DNS_TYPE="aly"
# 阿里dns
ALY_KEY=''
ALY_TOKEN=''
# 腾讯dns
TXY_KEY=""
TXY_TOKEN=""
# 后置钩子, 可以在申请后执行一些命令(如: 在此移动证书文件到指定目录, 重启nginx等)
#POST_HOOK="docker restart openresty"
```


生成的证书目录:

> /etc/letsencrypt/archive/[域名]

里面包含的证书内容：

```
cert1.pem: 服务端证书
chain1.pem: 浏览器需要的所有证书但不包括服务端证书，比如根证书和中间证书
fullchain1.pem: 包括了cert.pem和chain.pem的内容; nginx中ssl_certificate使用这个
privkey1.pem: 证书的私钥； nginx中ssl_certificate_key使用这个
```


