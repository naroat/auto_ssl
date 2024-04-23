### 功能

该脚本使用certbot生成证书分发到指定服务器, 实现一台机器管理证书, 可自动去阿里云(还包括腾讯云)创建dns记录完成验证，并自动部署ssl文件，只需设置一下配置文件即可

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

```
# 1: 使用前拷贝配置模板例子, 将其中的参数换成自己的即可
cp  ./conf.d/*.example.com.conf ./conf.d/*.example.com.conf
# 2: 执行脚本, 只用传递配置名称
./auto_ssl.sh *.example.com.conf    
```


