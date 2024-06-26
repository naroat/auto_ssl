#!/bin/bash
###### param check ######
if [[ ! -n $1 ]]; then
  echo "param error: please specify a configuration file!"
  exit
fi

###### read configuration ######
#CONF_FILE="$(pwd)/conf.d/$1"
CONF_FILE="$1"

# read
. ${CONF_FILE}

# auto_dns.sh path
AU_SH_PATH='./auto_dns.sh'

# dns: key and token
key=$KEYID
token=$TOEKN

# build param
DOMAIN_NAME_D=""
for domain_name in ${DOMAIN_NAMES[@]}
do
  DOMAIN_NAME_D+=" -d ${domain_name}"
done
#echo -e ${DOMAIN_NAME_D}

###### gen cert ######
certbot certonly ${DOMAIN_NAME_D} \
--manual \
--preferred-challenges dns \
--manual-auth-hook "${AU_SH_PATH} ${DNS_TYPE} add ${key} ${token}" \
--manual-cleanup-hook "${AU_SH_PATH} ${DNS_TYPE} clean ${key} ${token}" \
--post-hook "${POST_HOOK}"