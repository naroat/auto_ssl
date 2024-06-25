<?php


namespace App\Dns;


abstract class AbstractDns
{
    /*
      根据域名返回主机名和二级域名
     */
    public static function getDomain($domain)
    {
        //https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains
        $arr = self::config('domain_suffix');

        //二级域名
        $seconddomain = "";
        //子域名
        $selfdomain   = "";
        //根域名
        $rootdomain   = "";
        foreach ($arr as $k => $v) {
            $pos = stripos($domain, $v);
            if ($pos) {
                $rootdomain   = substr($domain, $pos);
                $s            = explode(".", substr($domain, 0, $pos));
                $seconddomain = $s[count($s) - 1].$rootdomain;
                for ($i = 0; $i < count($s) - 1; $i++)
                    $selfdomain .= $s[$i] . ".";
                $selfdomain = substr($selfdomain,0,strlen($selfdomain)-1);
                break;
            }
        }
        //echo $seconddomain ;exit;
        if ($rootdomain == "") {
            $seconddomain = $domain;
            $selfdomain   = "";
        }
        return array($selfdomain, $seconddomain);
    }

    public static function config($key)
    {
        $config = require_once BASE_PATH . '/config/config.php';
        return $config[$key] ?? null;
    }
}