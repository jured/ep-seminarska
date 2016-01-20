<?php

require './Queries.php';

class DB
{

    private static $_instance = NULL;

    private $dbc;

    public function __construct($location = "localhost:8889", $db_name = 'shop', $username = 'root', $password = 'root')
    {

        $dbc = new PDO("mysql:host=$location;dbname=" . $db_name, $username, $password);
        $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbc = $dbc;

        $this->get_all_products = $dbc->prepare(Queries::$all_products);
        $this->delete_product = $dbc->prepare(Queries::$delete_product);
        $this->create_update_product = $dbc->prepare(Queries::$create_update_product);

        $this->get_user_mail = $dbc->prepare(Queries::$user_by_mail);
        $this->get_user_by_token = $dbc->prepare(Queries::$user_by_token);
        $this->register_update_user = $dbc->prepare(Queries::$register_update_user);
        $this->add_token = $dbc->prepare(Queries::$add_token);
        $this->delete_token = $dbc->prepare(Queries::$delete_token);

        $this->place_update_order = $dbc->prepare(Queries::$place_update_order);
        $this->place_update_order_product_link = $dbc->prepare(Queries::$place_update_order_product_link);
        $this->all_orders = $dbc->prepare(Queries::$all_orders);
        $this->update_order_status = $dbc->prepare(Queries::$update_order_status);
        $this->users_by_kind = $dbc->prepare(Queries::$users_by_kind);
        $this->change_user_active = $dbc->prepare(Queries::$change_user_active);


    }

    // Products
    public function get_all_products()
    {
        if ($this->get_all_products->execute()) {
            return $this->get_all_products->fetchAll();
        } else {
            return false;
        }
    }

    public function delete_product($product_id)
    {
        return $this->delete_product->execute(array(':product_id' => $product_id));
    }

    public function create_update_product($product)
    {
        $active = false;
        if (array_key_exists('active', $product)) {
            $active = $product['active'] != null ? $product['active'] : true;
        }

        $product_id = null;
        if (array_key_exists('product_id', $product)) {
            $product_id = $product['product_id'];
        }

        return $this->create_update_product->execute(
            array(':product_id' => $product_id,
                ':name' => $product['name'],
                ':description' => $product['description'],
                ':price' => $product['price'],
                ':active' => $active));
    }

    // Users
    public function user_register($user)
    {

        if (!isset($user['email']) || !isset($user['name'])|| !isset($user['lastname']) || !isset($user['password'])) {
            return false;
        }

        $success = $this->get_user_mail->execute(array('email' => $user['email']));
        if ($success && !$this->get_user_mail->fetch()) {
            return $this->user_create_update($user);
        }
        return false;
    }

    public function user_create_update($user)
    {

        if (!isset($user['email']) || !isset($user['name'])|| !isset($user['lastname']) || !isset($user['password'])) {
            return false;
        }

        $password = null;
        if (!isset($user['password']) || isset($user['password']) && $user['password'] == '') {
            $password = null;
        } else {
            $password = password_hash($user['password'], PASSWORD_DEFAULT);
        }

        $created = $this->register_update_user->execute(
            array(':user_id' => isset($user['user_id']) ? $user['user_id'] : null,
                ':name' => isset($user['name']) ? $user['name'] : null,
                ':lastname' => isset($user['lastname']) ? $user['lastname'] : null,
                ':email' => isset($user['email']) ? $user['email'] : null,
                ':password' => $password,
                ':kind' => isset($user['kind']) ? $user['kind'] : 'costumer',
                ':address' => isset($user['address']) ? $user['address'] : null,
                ':phone' => isset($user['phone']) ? $user['phone'] : null,
                ':active' => true));

        return $created;

    }

    public function user_login($email, $password)
    {

        if ($this->get_user_mail->execute(array('email' => $email))) {
            $user = $this->get_user_mail->fetch();

            if ($user && $user['active'] && $password != null && password_verify($password, $user['password'])) {
                $user['token'] = uniqid();
                $this->add_token->execute(array(':token' => $user['token'], ':user_id' => $user['user_id']));
                return $user;
            }
        }

        return false;
    }

    public function user_by_token($token)
    {
        if ($this->get_user_by_token->execute(array(':token' => $token))) {
            $user = $this->get_user_by_token->fetch();
            if ($user) {
                return $user;
            }
        }
        return false;
    }

    public function user_logout($token)
    {
        return $this->delete_token->execute(array(':token' => $token));
    }

    public function change_user_status($userId, $status)
    {
        return $this->change_user_active->execute(array(':user_id' => $userId, 'active' => $status == 'active'));
    }

    public function order_create_update($user_token, $order)
    {

        $user = $this->user_by_token($user_token);
        if ($user) {
            $s = $this->place_update_order->execute(array(':order_id' => null,
                ':costumer_id' => $user['user_id'],
                ':seller_id' => null,
                ':status' => 'confirmed',
                ':date_modified' => null));
            $order_id = $this->dbc->lastInsertId();
            foreach ($order as $p) {
                $product = $p['item'];
                $product_id = $product['product_id'] != null ? $product['product_id'] : $this->dbc->lastInsertId();
                $this->place_update_order_product_link->execute(array(':product_id' => $product_id, ':order_id' => $order_id, ':number' => $p['number']));
            }

            return $s;
        }
        return false;
    }

    public function all_orders()
    {
        if ($this->all_orders->execute()) {
            $orders = array();
            foreach ($this->all_orders->fetchAll() as $o) {
                $order = null;
                if (!array_key_exists($o['order_id'], $orders)) {
                    $order = array();
                    $order['order_id'] = $o['order_id'];
                    $order['costumer_id'] = $o['costumer_id'];
                    $order['seller_id'] = $o['seller_id'];
                    $order['status'] = $o['status'];
                    $order['date_modified'] = $o['date_modified'];
                    $order['email'] = $o['email'];
                    $order['products'] = array();
                    $orders[$o['order_id']] = $order;
                }
                $product = array('name' => $o['name'], 'description' => $o['description'], 'price' => $o['price'],
                    'active' => $o['active'], 'image' => null, 'product_id' => $o['product_id'], 'number' => $o['number']);
                array_push($orders[$o['order_id']]['products'], $product);
            }

            return $orders;
        }
        return false;
    }

    public function orders_for_user($token)
    {
        $user = $this->user_by_token($token);
        $orders = $this->all_orders();
        if ($user && $orders) {
            $users_orders = array();
            foreach ($orders as $o) {
                if ($o['costumer_id'] == $user['user_id']) {
                    array_push($users_orders, $o);
                }
            }

            return $users_orders;
        }
        return false;
    }

    public function set_order_status($token, $orderId, $status)
    {
        $seller = $this->user_by_token($token);

        return $this->update_order_status->execute(
            array(':status' => $status,
                ':order_id' => $orderId,
                ':seller_id' => $seller['user_id']));
    }

    public function get_all_users_by_kind($kind)
    {
        if ($this->users_by_kind->execute(array(':kind' => $kind))) {
            return $this->users_by_kind->fetchAll();
        }
        return false;
    }

    public function isSSL()
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


    // Other
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

}