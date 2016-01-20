<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/user/{token}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    $user = DB::getInstance()->user_by_token($args['token']);

    if ($user) {
        return $response->getBody()->write(json_encode($user));
    } else {
        return $response->withStatus(400);
    }
});

$app->post('/user/login', function (ServerRequestInterface $request, ResponseInterface $response) {

    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    $login_data = json_decode($request->getBody(), true);

    if (!isset($login_data['email']) || !isset($login_data['password'])) {
        return $response->withStatus(300);
    }


    $user = DB::getInstance()->user_login($login_data['email'], $login_data['password']);

    if ($user) {
        return $response->getBody()->write(json_encode($user));
    } else {
        return $response->withStatus(400);
    }
});

$app->get('/user/{token}/logout', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    if (DB::getInstance()->user_logout($args['token'])) {
        return $response->withStatus(200);
    }
    return $response->withStatus(400);

});

$app->post('/register', function (ServerRequestInterface $request, ResponseInterface $response) {

    DB::getInstance()->isSSL();

    $response = $response->withHeader('Content-type', 'application/json');

    $user = DB::getInstance()->user_register(json_decode($request->getBody(), true));

    if ($user) {
        $body = array("success" => $user);
        return $response->getBody()->write(json_encode($body));
    } else {
        return $response->withStatus(400);
    }
});

$app->post('/user/{token}/create/order', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');

    $orders = DB::getInstance()->order_create_update($args['token'], json_decode($request->getBody(), true));
    if ($orders) {
        return $response->withStatus(200);
    } else {
        return $response->withStatus(400);
    }
});

$app->get('/user/{token}/orders', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();

    $response = $response->withHeader('Content-type', 'application/json');
    $user = DB::getInstance()->user_by_token($args['token']);
    if ($user['kind'] == 'costumer') {
        return $response->getBody()->write(json_encode(DB::getInstance()->orders_for_user($args['token'])));
    } else {
        return $response->getBody()->write(json_encode(DB::getInstance()->all_orders()));
    }
});
