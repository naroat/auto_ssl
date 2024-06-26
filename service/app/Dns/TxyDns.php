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


    public function __construct($accessKeyId, $accessSecrec, $recordValue = '') {
        $this->accessKeyId = $accessKeyId;
        $this->accessSecrec = $accessSecrec;
        $this->recordValue = $recordValue;
    }

    public function handle($action, $domain, $hostname)
    {
        $domainarray = self::getDomain($domain);
        $selfdomain = ($domainarray[0] == "") ? $hostname : $hostname . "." . $domainarray[0];
        $this->DomainName = $domainarray[1];

        switch ($action) {
            case "clean":
                $data = $this->RecordList($selfdomain, "TXT");
                if ($data["code"] != 0) {
                    throw new \Exception('txy dns get: ' . $data["message"]);
                }
                $records = $data["data"]["records"];
                foreach ($records as $k => $v) {

                    $data = $this->RecordDelete($v["id"]);

                    if ($data["code"] != 0) {
                        throw new \Exception('txy dns delete: ' . $data["message"]);
                    }
                }
                break;
            case "add":
                $data = $this->RecordCreate($selfdomain, "TXT", $this->recordValue);
                if ($data["code"] != 0) {
                    throw new \Exception('txy dns add: ' . $data["message"]);
                }
                break;
        }
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

    //sign
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