<?php

// RESTful API
// (c) Antti Stenvall
// antti@stenvall.fi
//

    error_reporting(E_ALL);
    @ini_set('display_errors', '1');

require_once '../vendor/autoload.php';
require_once '../model/temperature.php';

$app = new \Slim\Slim();

$app->get('/temperature', function() use ($app){
    $t = new temperature();
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode($t->getTemperatures()));
});

$app->run();

?>
