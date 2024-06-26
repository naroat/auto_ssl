<?php
########
# argv 1: domain provider
# argv 2: action; enum (add/clean)
# argv 3: domain
# argv 4: hostname
# argv 5: TXT value
# argv 6: APPKEY
# argv 7: APPTOKEN

######## example:
# php auto_dns.php aly add  "simplehttps.com" "hostname" "record value"  APPKEY APPTOKEN

try {
    if (count($argv) < 7) {
        throw new \Exception("param error");
    }

    ! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

    require BASE_PATH . '/vendor/autoload.php';

    echo ">>> start..." . PHP_EOL;

    switch ($argv[1]) {
        case "aly":
            (new \App\Dns\AlyDns($argv[6], $argv[7], $argv[5]))->handle($argv[2], $argv[3], $argv[4]);
            break;
        case "txy":
            (new \App\Dns\TxyDns($argv[6], $argv[7], $argv[5]))->handle($argv[2],$argv[3],  $argv[4]);
            break;
        case "godaddy":
            (new \App\Dns\GodaddyDns($argv[6], $argv[7], $argv[5]))->handle($argv[2], $argv[3], $argv[4]);
            break;
        default:
            throw new \Exception('Unsupported Domain provider');
            break;
    }

    echo ">>> success！" . PHP_EOL;

} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit;
}








