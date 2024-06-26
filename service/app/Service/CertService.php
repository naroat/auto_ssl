<?php


class CertService
{
    public function certInfo($url){
        if (!extension_loaded('openssl') || !is_callable('openssl_x509_parse')){
            throw new \Exception('Please enable the openssl extension for php');
        }

        $parse = parse_url($url);
        if (!empty($parse['host'])) {
            $domain = $parse['host'];
        } elseif (empty($parse['path'])) {
            throw new \Exception('domain error');
        } else {
            $arr = explode('/', $parse['path']);
            $domain = $arr[0];
        }
        //create stream
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'capture_peer_cert_chain' => true,
            ],
        ]);

        //create connection
        $client = @stream_socket_client("ssl://" . $domain . ":443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
        if ($client == false) {
            return ['code' => -1, 'msg' => $domain . '未查到可靠信息','err'=>[
                'errno'=>$errno,
                'errstr'=>iconv('gbk', 'utf-8', $errstr),
            ]];
        }
        //get context
        $params = stream_context_get_params($client);
        if (empty($params['options']['ssl']['peer_certificate'])) {
            return ['code' => -1, 'msg' => $domain . '获取信息失败，请确保可以正常访问'];
        }
        $cert = $params['options']['ssl']['peer_certificate'];
        $cert_info = @openssl_x509_parse($cert);
        return ['code' => 0, 'data' => $cert_info];
    }
}