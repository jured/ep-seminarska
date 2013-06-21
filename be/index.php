<?php

require '../vendor/autoload.php';
require './DB.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app = new Slim\App();

function isSSL()
{
    if (!((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443
        || (
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
        ))
    ) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], TRUE, 301);
        exit;
    }
}

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
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');

    error_log("Updating or editing self " . $request->getBody());

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

// Costumer
$app->get('/user/{token}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');

    error_log("Retrieving user data by token: " . $args['token']);
    $user = DB::getInstance()->user_by_token($args['token']);

    if ($user) {
        return $response->getBody()->write(json_encode($user));
    } else {
        return $response->withStatus(400);
    }
});

$app->post('/user/login', function (ServerRequestInterface $request, ResponseInterface $response) {

    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');

    error_log("Logging in user: " . $request->getBody());
    $login_data = json_decode($request->getBody(), true);

    if (!isset($login_data['email']) || !isset($login_data['password'])) {
        return $response->withStatus(300);
    }


    $user = DB::getInstance()->user_login($login_data['email'], $login_data['password']);
    // TODO Must api with certificate
    //    if ($r['userlevel'] != 'costumer' && !array_key_exists('SSL_CLIENT_S_DN_Email', $_SERVER)) {
    //          return $response->withStatus(403);
    //    }

    if ($user) {
        return $response->getBody()->write(json_encode($user));
    } else {
        return $response->withStatus(400);
    }
});

$app->get('/user/{token}/logout', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    if (DB::getInstance()->user_logout($args['token'])) {
        return $response->withStatus(200);
    }
    return $response->withStatus(400);

});

$app->post('/register', function (ServerRequestInterface $request, ResponseInterface $response) {

    isSSL();
    error_log("Registering or modifying user: " . $request->getBody());

    $response = $response->withHeader('Content-type', 'application/json');

    error_log("here1");
    $user = DB::getInstance()->user_register(json_decode($request->getBody(), true));

    error_log("here2");
    if ($user) {
        $body = array("success" => $user);
        return $response->getBody()->write(json_encode($body));
    } else {
        return $response->withStatus(400);
    }
});

$app->post('/user/{token}/create/order', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');

    error_log("Placing order: " . $request->getBody());
    $orders = DB::getInstance()->order_create_update($args['token'], json_decode($request->getBody(), true));
    if ($orders) {
        return $response->withStatus(200);
    } else {
        return $response->withStatus(400);
    }
});

$app->get('/user/{token}/orders', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();

    $response = $response->withHeader('Content-type', 'application/json');
    $user = DB::getInstance()->user_by_token($args['token']);
    if ($user['kind'] == 'costumer') {
        return $response->getBody()->write(json_encode(DB::getInstance()->orders_for_user($args['token'])));
    } else {
        return $response->getBody()->write(json_encode(DB::getInstance()->all_orders()));
    }
});

// Seller
function isSeller($token)
{
    $user = DB::getInstance()->user_by_token($token);
    return $user['kind'] == 'seller';
}

$app->get('/seller/{token}/order/{orderId}/status/{status}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    error_log("Updating order status for " . $args['token'] . ", " . $args['orderId'] . ", " . $args['status']);
    if (DB::getInstance()->set_order_status($args['token'], $args['orderId'], $args['status'])) {
        return $response->withStatus(200);
    }

    return $response->withStatus(500);
});

$app->delete('/seller/{token}/product/{itemId}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    DB::getInstance()->delete_product($args['itemId']);
    return $response->withStatus(200);
});

$app->post('/seller/{token}/create-update/product', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    error_log("Creating or updating product " . $request->getBody());
    DB::getInstance()->create_update_product(json_decode($request->getBody(), true));

    return $response->withStatus(200);
});

$app->get('/seller/{token}/costumers', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
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
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    error_log("Updating or editing costumer " . $request->getBody());

    $edited_user = json_decode($request->getBody(), true);
    $edited_user['kind'] = 'costumer';
    if (DB::getInstance()->user_create_update($edited_user)) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});

$app->get('/seller/{token}/status/costumer/{userId}/{status}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isSeller($args['token'])) {
        return $response->withStatus(400);
    }

    error_log("Updating costumer status " . $args['userId'] . ", " . $args['status']);

    if (DB::getInstance()->change_user_status($args['userId'], $args['status'])) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});

// Admin
function isAdmin($token)
{
    $user = DB::getInstance()->user_by_token($token);
    return $user['kind'] == 'admin';
}

$app->get('/admin/{token}/sellers', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
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
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isAdmin($args['token'])) {
        return $response->withStatus(400);
    }

    error_log("Updating or editing seller " . $request->getBody());

    $edited_user = json_decode($request->getBody(), true);
    $edited_user['kind'] = 'seller';
    if (DB::getInstance()->user_create_update($edited_user)) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});

$app->get('/admin/{token}/status/seller/{userId}/{status}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    isSSL();
    $response = $response->withHeader('Content-type', 'application/json');
    if (!isAdmin($args['token'])) {
        return $response->withStatus(400);
    }

    error_log("Updating costumer status " . $args['userId'] . ", " . $args['status']);

    if (DB::getInstance()->change_user_status($args['userId'], $args['status'])) {
        return $response->withStatus(200);
    }
    return $response->withStatus(500);
});

$app->run();
