<?php
namespace AutoSsl\Service\App;

class GodaddyDns extends AbstractDns
{
    private $accessKeyId  = null;
    private $accessSecrec = null;
    private $DomainName   = null;
    private $recordValue = '';

    public function __construct($accessKeyId, $accessSecrec, $recordValue = '')
    {
        $this->accessKeyId  = $accessKeyId;
        $this->accessSecrec = $accessSecrec;
        $this->recordValue  = $recordValue;
    }

    public function handle($action, $domain, $hostname)
    {
        $domainarray = self::getDomain($domain);
        $selfdomain  = ($domainarray[0] == "") ? $hostname : $hostname.".".$domainarray[0];
        $this->DomainName = $domainarray[1];

        switch ($action) {
            case "clean":
                //api
                break;

            case "add":
                //$data     = $obj->GetDNSRecord($domainarray[1], $selfdomain);
                //$data_obj = json_decode($data['result']);
                //$count    = count($data_obj);
                //if ($count > 0) {

                //    $data = $obj->UpdateDNSRecord($domainarray[1], $selfdomain, $argv[4]);
                //} else {
                $data = $this->CreateDNSRecord($domainarray[1], $selfdomain, $this->recordValue);
                //}
                if ($data["httpCode"] != 200) {
                    $message = json_decode($data["result"], true);
                    throw new \Exception('error' . $message["message"]);
                }
                break;
        }
    }

    private function curl($url, $header = '', $data = '', $method = 'get')
    {
        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array(
            'result' => $result,
            'httpCode' => $httpCode
        );
    }

    public function getDomains()
    {

        $url    = "https://api.godaddy.com/v1/domains";
        $header = ['accept: application/json', 'authorization:sso-key '.$this->accessKeyId.':'.$this->accessSecrec];
        return $this->curl($url, $header);
    }

    public function delRecords($domain)
    {

        $url    = "https://api.godaddy.com/v1/domains/$domain";
        $header = ['accept: application/json', 'Content-Type: application/json',
            'authorization:sso-key '.$this->accessKeyId.':'.$this->accessSecrec];

        return $this->curl($url, $header, '', 'delete');
    }

    public function GetDNSRecord($domain, $record, $recordType = 'TXT')
    {
        $url    = "https://api.godaddy.com/v1/domains/$domain/records/$recordType/$record";
        $header = ['accept: application/json', 'authorization:sso-key '.$this->accessKeyId.':'.$this->accessSecrec];
        return $this->curl($url, $header);
    }

    public function UpdateDNSRecord($domain, $name, $value, $recordType = 'TXT')
    {
        $url    = "https://api.godaddy.com/v1/domains/$domain/records/$recordType/$name";
        $header = ['accept: application/json', 'Content-Type: application/json',
            'authorization:sso-key '.$this->accessKeyId.':'.$this->accessSecrec];
        $data   = array(
            array(
                'data' => $value,
                'name' => $name,
                'ttl' => 3600,
                'type' => $recordType)
        );
        return $this->curl($url, $header, json_encode($data), 'put');
    }

    public function CreateDNSRecord($domain, $name, $value, $recordType = 'TXT')
    {
        $url    = "https://api.godaddy.com/v1/domains/$domain/records";
        $header = ['accept: application/json', 'Content-Type: application/json',
            'authorization:sso-key '.$this->accessKeyId.':'.$this->accessSecrec];
        $data   = array(
            array(
                'data' => $value,
                'name' => $name,
                'ttl' => 3600,
                'type' => $recordType)
        );
        return $this->curl($url, $header, json_encode($data), 'PATCH');
    }
}
