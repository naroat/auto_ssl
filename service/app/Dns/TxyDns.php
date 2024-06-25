<?php

namespace AutoSsl\Service\App;

class TxyDns extends AbstractDns
{

    private $accessKeyId = null;
    private $accessSecrec = null;
    private $DomainName = null;
    private $recordValue = '';
    private $Host = "cns.api.qcloud.com";
    private $Path = "/v2/index.php";


    public function __construct($accessKeyId, $accessSecrec, $domain, $recordValue = '_acme-challenge') {
        $this->accessKeyId = $accessKeyId;
        $this->accessSecrec = $accessSecrec;
        $this->DomainName = $domain;
        $this->recordValue = $recordValue;
    }

    public function handle($action, $hostname)
    {
        $domainarray = self::getDomain($this->DomainName);
        $selfdomain = ($domainarray[0] == "") ? $hostname : $hostname . "." . $domainarray[0];

        switch ($action) {
            case "clean":
                $data = $this->RecordList($selfdomain, "TXT");
                if ($data["code"] != 0) {
                    echo "txy dns 记录获取失败-" . $data["message"] . "\n";
                    exit;
                }
                $records = $data["data"]["records"];
                foreach ($records as $k => $v) {

                    $data = $this->RecordDelete($v["id"]);

                    if ($data["code"] != 0) {
                        echo "txy dns 记录删除失败-" . $data["message"] . "\n";
                        exit;
                    }
                }

                break;

            case "add":
                $data = $this->RecordCreate($selfdomain, "TXT", $this->recordValue);
                if ($data["code"] != 0) {
                    echo "txy dns 记录添加失败-" . $data["message"] . "\n";
                    exit;
                }
                break;
        }

        echo "域名 API 调用成功结束\n";
    }

    public function error($code, $str) {
        echo "操作错误:" . $code . ":" . $str;
        exit;
    }

    public function RecordDelete($recordId) {
        $param["domain"] = $this->DomainName;
        $param["recordId"] = $recordId;

        $data = $this->send("RecordDelete", "GET", $param);
        return ($this->out($data));
    }

    public function RecordList($subDomain, $recordType = "") {

        if ($recordType != "")
            $param["recordType"] = $recordType;
        $param["subDomain"] = $subDomain;
        $param["domain"] = $this->DomainName;

        $data = $this->send("RecordList", "GET", $param);
        return ($this->out($data));
    }

    public function RecordModify($subDomain, $recordType = "TXT", $value, $recordId) {
        $param["recordType"] = $recordType;
        $param["subDomain"] = $subDomain;
        $param["recordId"] = $recordId;
        $param["domain"] = $this->DomainName;
        $param["recordLine"] = "默认";
        $param["value"] = $value;

        $data = $this->send("RecordModify", "GET", $param);
        return ($this->out($data));
    }

    public function RecordCreate($subDomain, $recordType = "TXT", $value) {
        $param["recordType"] = $recordType;
        $param["subDomain"] = $subDomain;
        $param["domain"] = $this->DomainName;
        $param["recordLine"] = "默认";
        $param["value"] = $value;

        $data = $this->send("RecordCreate", "GET", $param);
        return ($this->out($data));
    }

    public function DomainList() {

        $data = $this->send("DomainList", "GET", array());
        return ($this->out($data));
    }

    private function send($action, $reqMethod, $requestParams) {

        $params = $this->formatRequestData($action, $requestParams, $reqMethod);

        $uri = http_build_query($params);
        $url = "https://" . $this->Host . "" . $this->Path . "?" . $uri;
        return $this->curl($url);
    }

    private function formatRequestData($action, $request, $reqMethod) {
        $param = $request;
        $param["Action"] = ucfirst($action);
//$param["RequestClient"] = $this->sdkVersion;
        $param["Nonce"] = rand();
        $param["Timestamp"] = time();
//$param["Version"] = $this->apiVersion;

        $param["SecretId"] = $this->accessKeyId;

        $signStr = $this->formatSignString($this->Host, $this->Path, $param, $reqMethod);
        $param["Signature"] = $this->sign($signStr);
        return $param;
    }

//签名
    private function formatSignString($host, $path, $param, $requestMethod) {
        $tmpParam = array();
        ksort($param);
        foreach ($param as $key => $value) {
            array_push($tmpParam, str_replace("_", ".", $key) . "=" . $value);
        }
        $strParam = join("&", $tmpParam);
        $signStr = strtoupper($requestMethod) . $host . $path . "?" . $strParam;
        return $signStr;
    }

    private function sign($signStr) {

        $signature = base64_encode(hash_hmac("sha1", $signStr, $this->accessSecrec, true));
        return $signature;
    }

    private function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function out($msg) {
        return json_decode($msg, true);
    }

}