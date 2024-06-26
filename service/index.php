<?php
use Hyperf\Nano\Factory\AppFactory;

require_once __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function () {

    $user = $this->request->input('user', 'nano');
    $method = $this->request->getMethod();

    return [
        'message' => "hello {$user}",
        'method' => $method,
    ];

});

$app->get('/ssl/info', function () {

    $domain = 'https://tools.ranblogs.com';
//    $info

    return [
        'message' => "hello {$user}",
        'method' => $method,
    ];

});

$app->run();
