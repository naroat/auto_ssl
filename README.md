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

## 自动续签

### 续签配置

续签配置(申请时自动生成)
```shell
/etc/letsencrypt/renewal
```

### 续签命令

#### 通过命令续签
```shell
# 续签证书
sudo certbot renew 

# 测试续签
sudo certbot renew --dry-run
```

#### 通过定时任务续签
```shell
# 编辑 crontab
sudo crontab -e

# 每天凌晨 3 点续签（非交互式，必须加该参数）
0 3 * * * /usr/bin/certbot renew --quiet --post-hook "systemctl reload nginx"2>&1 | mail -s "Certbot 续签报告" admin@admin.com
```

### 问题

#### manual-auth-hook 路径错误

错误信息
```shell
Attempting to renew cert (example.com) from /etc/letsencrypt/renewal/example.com.conf produced an unexpected error: Unable to find manual-auth-hook command ./dns-au/au.sh in the PATH. 
 (PATH is /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/snap/bin). Skipping. 
 All renewal attempts failed. The following certs could not be renewed: 
   /etc/letsencrypt/live/example.com/fullchain.pem (failure)
```

解决方法: 修改续签配置文件，将 manual-auth-hook 路径改为绝对路径,示例如下`./dns-au/au.sh` 改成 `/你的路径/dns-au/au.sh`
```
[renewalparams]
authenticator = manual
manual_auth_hook = /root/dns-au/au.sh      # ← 改为绝对路径
manual_cleanup_hook = /root/dns-au/au.sh clean  # 如果有清理脚本
account = xxxxxxx
```

#### --manual-public-ip-logging-ok 错误

错误信息
```shell
Plugins selected: Authenticator manual, Installer None 
 Renewing an existing certificate 
 Performing the following challenges: 
 dns-01 challenge for example.com 
 Cleaning up challenges 
 Encountered exception during recovery: 
 Traceback (most recent call last): 
   File "/usr/lib/python3/dist-packages/certbot/auth_handler.py", line 70, in handle_authorizations 
     resps = self.auth.perform(achalls) 
   File "/usr/lib/python3/dist-packages/certbot/plugins/manual.py", line 112, in perform 
     self._verify_ip_logging_ok() 
   File "/usr/lib/python3/dist-packages/certbot/plugins/manual.py", line 133, in _verify_ip_logging_ok 
     if display.yesno(msg, cli_flag=cli_flag, force_interactive=True): 
   File "/usr/lib/python3/dist-packages/certbot/display/util.py", line 536, in yesno 
     self._interaction_fail(message, cli_flag) 
   File "/usr/lib/python3/dist-packages/certbot/display/util.py", line 466, in _interaction_fail 
     raise errors.MissingCommandlineFlag(msg) 
 certbot.errors.MissingCommandlineFlag: Missing command line flag or config entry for this setting: 
 NOTE: The IP of this machine will be publicly logged as having requested this certificate. If you're running certbot in manual mode on a machine that is not your server, please ensure you're okay with that. 
 
 Are you OK with your IP being logged? 
 
 (You can set this with the --manual-public-ip-logging-ok flag) 
 
 During handling of the above exception, another exception occurred: 
 
 Traceback (most recent call last): 
   File "/usr/lib/python3/dist-packages/certbot/error_handler.py", line 124, in _call_registered 
     self.funcs[-1]() 
   File "/usr/lib/python3/dist-packages/certbot/auth_handler.py", line 243, in _cleanup_challenges 
     self.auth.cleanup(achalls) 
   File "/usr/lib/python3/dist-packages/certbot/plugins/manual.py", line 177, in cleanup 
     env = self.env.pop(achall) 
 KeyError: KeyAuthorizationAnnotatedChallenge(challb=ChallengeBody(chall=DNS01(token=b'\x9d\xcd\xc9*\xa5\xed\x0f\x0f.r\xeb\xf3\x1bU5\xca\x7f\xbdS.\xecm\xaf\xee\xee)\x11\x0fW\x14\xadz'), uri=' https://acme-staging-v02.api.letsencrypt.org/acme/chall/331123183/4116968023/hl90bQ ', _url=' https://acme-staging-v02.api.letsencrypt.org/acme/chall/331123183/4116968023/hl90bQ ', status=Status(pending), validated=None, error=None), domain='example.com', account_key=JWKRSA(key=<ComparableRSAKey(<cryptography.hazmat.backends.openssl.rsa._RSAPrivateKey object at 0x7f1a3a99bdc0>)>)) 
 Attempting to renew cert (example.com) from /etc/letsencrypt/renewal/example.com.conf produced an unexpected error: Missing command line flag or config entry for this setting: 
 NOTE: The IP of this machine will be publicly logged as having requested this certificate. If you're running certbot in manual mode on a machine that is not your server, please ensure you're okay with that. 
 
 Are you OK with your IP being logged? 
 
 (You can set this with the --manual-public-ip-logging-ok flag). Skipping. 
 All renewal attempts failed. The following certs could not be renewed: 
   /etc/letsencrypt/live/example.com/fullchain.pem (failure)
```

解决方法: 设置`manual_public_ip_logging_ok` 为 `True`
```shell
[renewalparams]
authenticator = manual
manual_auth_hook = /root/dns-au/au.sh        # 确认是绝对路径
manual_public_ip_logging_ok = True            # ← 新增这一行
account = xxxxxxx
```
