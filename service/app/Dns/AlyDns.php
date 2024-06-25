<?php

namespace App\Dns;

class AlyDns extends AbstractDns
{
	private $accessKeyId  = null;
	private $accessSecrec = null;
	private $DomainName   = null;
	private $recordValue = '';

	public function __construct($accessKeyId, $accessSecrec, $domain, $recordValue = '')
	{
		$this->accessKeyId  = $accessKeyId;
		$this->accessSecrec = $accessSecrec;
		$this->DomainName   = $domain;
		$this->recordValue = $recordValue;
	}

	public function handle($action, $hostname)
    {
        $domainarray = self::getDomain($this->DomainName);
        $selfdomain  = ($domainarray[0] == "") ? $hostname : $hostname.".".$domainarray[0];

        switch ($action) {
            case "clean":
                $data = $this->DescribeDomainRecords();
                $data = $data["DomainRecords"]["Record"];
                if (is_array($data)) {
                    foreach ($data as $v) {
                        if ($v["RR"] == $selfdomain) {
                            $data = $this->DeleteDomainRecord($v["RecordId"]);
                            if ($data["httpcode"] != 200) {
                                throw new \Exception('aly dns: Domain name deletion failed; ' . $data["Code"] . ":" . $data["Message"]);
                            }
                        }
                    }
                }
                break;

            case "add":
                $data = $this->AddDomainRecord("TXT", $selfdomain, $this->recordValue);

                if ($data["httpcode"] != 200) {
                    throw new \Exception('aly dns: Failed to add domain name; ' . $data["Code"] . ":" . $data["Message"]);
                }
                break;
        }
    }

	public function DescribeDomainRecords()
	{
		$requestParams = array(
			"Action" => "DescribeDomainRecords"
		);
		$val           = $this->send($requestParams);

		return $this->out($val);
	}

	public function UpdateDomainRecord($id, $type, $rr, $value)
	{
		$requestParams = array(
			"Action" => "UpdateDomainRecord",
			"RecordId" => $id,
			"RR" => $rr,
			"Type" => $type,
			"Value" => $value,
		);
		$val           = $this->send($requestParams);
		return $this->out($val);
	}

	public function DeleteDomainRecord($id)
	{
		$requestParams = array(
			"Action" => "DeleteDomainRecord",
			"RecordId" => $id,
		);
		$val           = $this->send($requestParams);
		return $this->out($val);
	}

	public function AddDomainRecord($type, $rr, $value)
	{

		$requestParams = array(
			"Action" => "AddDomainRecord",
			"RR" => $rr,
			"Type" => $type,
			"Value" => $value,
		);
		$val           = $this->send($requestParams);
		return $this->out($val);
	}

	private function send($requestParams)
	{
		$publicParams = array(
			"DomainName" => $this->DomainName,
			"Format" => "JSON",
			"Version" => "2015-01-09",
			"AccessKeyId" => $this->accessKeyId,
			"Timestamp" => date("Y-m-d\TH:i:s\Z"),
			"SignatureMethod" => "HMAC-SHA1",
			"SignatureVersion" => "1.0",
			"SignatureNonce" => substr(md5(rand(1, 99999999)), rand(1, 9), 14),
		);

		$params              = array_merge($publicParams, $requestParams);
		$params['Signature'] = $this->sign($params, $this->accessSecrec);
		$uri                 = http_build_query($params);
		$url                 = 'http://alidns.aliyuncs.com/?'.$uri;
		return $this->curl($url);
	}

	private function sign($params, $accessSecrec, $method = "GET")
	{
		ksort($params);
		$stringToSign = strtoupper($method).'&'.$this->percentEncode('/').'&';

		$tmp = "";
		foreach ($params as $key => $val) {
			$tmp .= '&'.$this->percentEncode($key).'='.$this->percentEncode($val);
		}
		$tmp          = trim($tmp, '&');
		$stringToSign = $stringToSign.$this->percentEncode($tmp);

		$key  = $accessSecrec.'&';
		$hmac = hash_hmac("sha1", $stringToSign, $key, true);

		return base64_encode($hmac);
	}

	private function percentEncode($value = null)
	{
		$en = urlencode($value);
		$en = str_replace("+", "%20", $en);
		$en = str_replace("*", "%2A", $en);
		$en = str_replace("%7E", "~", $en);
		return $en;
	}

	private function curl($url)
	{
		$ch     = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		$result = curl_exec($ch);
		$info   = curl_getinfo($ch);

		curl_close($ch);
		return array($info["http_code"], $result);
	}

	private function out($arr)
	{
		$t             = json_decode($arr[1], true);
		$t["httpcode"] = $arr[0];
		return $t;
	}
}