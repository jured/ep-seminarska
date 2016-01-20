<?php

// Admin
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function isAdmin($token)
{
    $user = DB::getInstance()->user_by_token($token);
    return $user['kind'] == 'admin';
}

$app->get('/admin/{token}/sellers', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isAdmin($args['token'])) {
        return $response->withStatus(400);
    }

    $costumers = DB::getInstance()->get_all_users_by_kind('seller');
    if ($costumers) {
        return $response->getBody()->write(json_encode($costumers));
    }

    return $response->withStatus(500);
});

$app->post('/admin/{token}/edit/seller', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isAdmin($args['token'])) {
        return $response->withStatus(400);
    }

    $edited_user = json_decode($request->getBody(), true);
    $edited_user['kind'] = 'seller';
    if (DB::getInstance()->user_create_update($edited_user)) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});

$app->get('/admin/{token}/status/seller/{userId}/{status}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    DB::getInstance()->isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isAdmin($args['token'])) {
        return $response->withStatus(400);
    }


    if (DB::getInstance()->change_user_status($args['userId'], $args['status'])) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});
