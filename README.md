## 自认证ssl脚本使用

### 脚本存放服务器
```
ssh root@120.77.201.214
Hanzikeji456
```

### 脚本目录

```
/disk2/soft/AutoSSL
```

### 目录结构
```
/disk2/soft/AutoSSL/conf.d/:         配置文件目录; 示例配置文件: *.example.com.conf
/disk2/soft/AutoSSL/auto_ssl.sh:     脚本文件, 需要一个参数; 传配置文件名称
/disk2/soft/AutoSSL/certbot-letencrypt-wildcardcertificates-alydns-au: 第三方插件, 用来设置和修改dns解析记录;
```

### 使用示例

```
# 1: 进入脚本目录
cd /disk2/soft/AutoSSL  
# 2: 使用前创建配置文件,并填写配置信息
vim  ./conf.d/*.example.com.conf
# 3: 执行脚本, 只用传递配置名称
./auto_ssl.sh *.example.com.conf    
```

### 注: 第三方插件中au.sh原本只能接受三个参数, 为了适应自身的脚本, 这里做了点修改如下:

> 第三方插件地址: https://github.com/ywdblog/certbot-letencrypt-wildcardcertificates-alydns-au

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
