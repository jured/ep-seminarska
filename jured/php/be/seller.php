<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function isSeller($token)
{
    $user = DB::getInstance()->user_by_token($token);
    return $user['kind'] == 'seller';
}

$app->get('/seller/{token}/order/{orderId}/status/{status}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    if (DB::getInstance()->set_order_status($args['token'], $args['orderId'], $args['status'])) {
        return $response->withStatus(200);
    }

    return $response->withStatus(500);
});

$app->delete('/seller/{token}/product/{itemId}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    DB::getInstance()->delete_product($args['itemId']);
    return $response->withStatus(200);
});

$app->post('/seller/{token}/create-update/product', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    DB::getInstance()->create_update_product(json_decode($request->getBody(), true));

    return $response->withStatus(200);
});

$app->get('/seller/{token}/costumers', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    $costumers = DB::getInstance()->get_all_users_by_kind('costumer');
    if ($costumers) {
        return $response->getBody()->write(json_encode($costumers));
    }

    return $response->withStatus(500);
});

$app->post('/seller/{token}/edit/costumer', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    $edited_user = json_decode($request->getBody(), true);
    $edited_user['kind'] = 'costumer';
    if (DB::getInstance()->user_create_update($edited_user)) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});

$app->get('/seller/{token}/status/costumer/{userId}/{status}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    if (DB::getInstance()->change_user_status($args['userId'], $args['status'])) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});