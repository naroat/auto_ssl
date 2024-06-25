<?php

/*
    example:
    php auto_dns.php aly add  "simplehttps.com" "dnsv" "dnsk"  APPKEY APPTOKEN


php74 auto_dns.php aly add  "wiki.ranblogs.com" "_acme-challenge" "dnsk"  LTAI5tBTau2u6uKDoADpnMu5 Kh5cUT6yH6lJQcMDb52USmlXgZrABs
*/

##########
# argv 1: domain provider
# argv 2: action，enum (add/clean)
# argv 3: domain
# argv 4: hostname
# argv 5: TXT record
# argv 6: APPKEY
# argv 7: APPTOKEN

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

var_dump('======');

try {
    if (count($argv) < 7) {
        throw new \Exception("param error");
    }

    ! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

    require BASE_PATH . '/vendor/autoload.php';

    switch ($argv[1]) {
        case "aly":
            (new \App\Dns\AlyDns($argv[6], $argv[7], $argv[3], $argv[5]))->handle($argv[2], $argv[4]);
            break;
        case "txy":
            (new \App\Dns\TxyDns($argv[6], $argv[7], $argv[3], $argv[5]))->handle($argv[2], $argv[4]);
            break;
        case "godaddy":
            (new \App\Dns\GodaddyDns($argv[6], $argv[7], $argv[3], $argv[5]))->handle($argv[2], $argv[4]);
            break;
        default:
            throw new \Exception('Unsupported Domain provider');
            break;
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit;
}








