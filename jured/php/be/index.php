<?php

require '../vendor/autoload.php';
require './DB.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app = new Slim\App();
error_log("app" . $app::VERSION);

require_once './costumer.php';
require_once './seller.php';
require_once './admin.php';

// Anonymus user
$app->get('/products/all', function (ServerRequestInterface $request, ResponseInterface $response) {
    $response = $response->withHeader('Content-type', 'application/json');

    $products = DB::getInstance()->get_all_products();
    if ($products) {
        return $response->getBody()->write(json_encode($products));
    } else {
        return $response->withStatus(500);
    }
});

// Common
$app->post('/self/{token}/edit', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');

    $self = DB::getInstance()->user_by_token($args['token']);
    $self_data = json_decode($request->getBody(), true);
    if ($self['user_id'] == $self_data['user_id']) {
        if (DB::getInstance()->user_create_update($self_data)) {
            return $response->withStatus(200);
        }
        return $response->withStatus(500);
    }
    return $response->withStatus(400);
});

$app->run();
