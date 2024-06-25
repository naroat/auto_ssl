#!/bin/bash

###### 根据自己的情况修改 Begin ##############

#PHP 命令行路径，如果有需要可以修改
phpcmd="/usr/local/bin/php"
#填写阿里云的AccessKey ID及AccessKey Secret
#如何申请见https://help.aliyun.com/knowledge_detail/38738.html
ALY_KEY=$3
ALY_TOKEN=$4

#填写腾讯云的SecretId及SecretKey
#如何申请见https://console.cloud.tencent.com/cam/capi
TXY_KEY=$3
TXY_TOKEN=$4

#GoDaddy的SecretId及SecretKey
#如何申请见https://developer.godaddy.com/getstarted
GODADDY_KEY=$3
GODADDY_TOKEN=$4

################ END ##############

PATH=$(cd `dirname $0`; pwd)

dnsapi=$PATH"/bin/auto_dns.php"

# 命令行参数
# 第二个参数：使用那个 DNS 的 API
# 第三个参数：add or clean
pdns=$1 #aly, txy, hwy, godaddy
paction=$2 #add or clean

#内部变量
cmd=$phpcmd
key=""
token=""
logfile=$PATH"/runtime/log.log"

if [[ "$paction" != "clean" ]]; then
	paction="add"
fi

if [[ "$pdns" == "aly" ]]; then
    key=$ALY_KEY
    token=$ALY_TOKEN
elif [[ "$pdns" == "txy" ]]; then
    key=$TXY_KEY
    token=$TXY_TOKEN
elif [[ "$pdns" == "godaddy" ]] ;then
    key=$GODADDY_KEY
    token=$GODADDY_TOKEN
else
    echo "Not support this dns services"
    exit
fi

#$cmd $dnsapi $pdns $paction $CERTBOT_DOMAIN "_acme-challenge" $CERTBOT_VALIDATION $key $token >>$logfile
$cmd $dnsapi $pdns $paction "wiki.ranblogs.com" "_acme-challenge" "123123123" $key $token >>$logfile

if [[ "$paction" == "add" ]]; then
        # DNS TXT 记录刷新时间
        /bin/sleep 20
fi

