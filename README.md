@[TOC]

### 功能

该脚本使用certbot生成证书分发到指定服务器, 实现一台机器管理证书, 只需要设置一下配置文件;

### 该脚本需要用到第三方插件修改dns记录值 

> 第三方插件地址: https://github.com/ywdblog/certbot-letencrypt-wildcardcertificates-alydns-au

> 第三方插件中au.sh原本只能接受三个参数, 为了适应自身的脚本, 这里做了点修改如下:
>
```
#如何申请见https://help.aliyun.com/knowledge_detail/38738.html
ALY_KEY=$4
ALY_TOKEN=$5

#填写腾讯云的SecretId及SecretKey
#如何申请见https://console.cloud.tencent.com/cam/capi
TXY_KEY=$4
TXY_TOKEN=$5

#填写华为云的 Access Key Id 及 Secret Access Key
#如何申请见https://support.huaweicloud.com/devg-apisign/api-sign-provide.html
HWY_KEY=$4
HWY_TOKEN=$5

#GoDaddy的SecretId及SecretKey
#如何申请见https://developer.godaddy.com/getstarted
GODADDY_KEY=$4
GODADDY_TOKEN=$5
```

### 目录结构
```
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


