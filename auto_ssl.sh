#!/bin/bash
###### 参数验证 ######
if [[ ! -n $1 ]]; then
  echo "参数错误: 需要指定一个配置文件!"
  exit
fi

###### 读取配置 ######
# 配置文件完整路径
#CONF_FILE="$(pwd)/conf.d/$1"
CONF_FILE="$1"

# 读取配置文件
. ${CONF_FILE}

# 脚本路径(第三方插件的脚本路径)
AU_SH_PATH='/disk2/soft/certbot-letencrypt-wildcardcertificates-alydns-au/au.sh'

# dns: key and token
key=""
token=""
if [[ $DNS_TYPE=="aly" ]]; then
  key=$ALY_KEY
  token=$ALY_TOKEN
elif [[ $DNS_TYPE=="txy" ]]; then
  key=$TXY_KEY
  token=$TXY_TOKEN
fi

# 组合参数
DOMAIN_NAME_D=""
for domain_name in ${DOMAIN_NAMES[@]}
do
  DOMAIN_NAME_D+=" -d ${domain_name}"
done
#echo -e ${DOMAIN_NAME_D}

###### 生成证书 ######
certbot certonly ${DOMAIN_NAME_D} \
--manual \
--preferred-challenges dns \
--manual-auth-hook "${AU_SH_PATH} ${SCRIPT_ENV} ${DNS_TYPE} add ${key} ${token}" \
--manual-cleanup-hook "${AU_SH_PATH} ${SCRIPT_ENV} ${DNS_TYPE} clean ${key} ${token}" \
--post-hook "${POST_HOOK}"