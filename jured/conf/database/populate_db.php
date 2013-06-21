<?php
chdir('../../../be/');

require './DB.php';


function test_get_all_products()
{
    $db = DB::getInstance();
    error_log(json_encode($db->get_all_products()));
}

function register_user($user)
{
    $db = DB::getInstance();
    $user = $db->user_create_update($user);
    error_log("Registered user:" . $user);
}

function test_user_login($email, $password)
{
    $db = DB::getInstance();
    $user = $db->user_login($email, $password);
    error_log("Logged in user by mail:" . json_encode($user));

}

function test_user_by_token($token) {
    $db = DB::getInstance();
    $user = $db->user_by_token($token);
    error_log("Logged in user by token:" . json_encode($user));
}

function test_user_logout($token) {
    $db = DB::getInstance();
    error_log("Was deleteing token successful: " . $db->user_logout($token));
}

function test_place_update_order($token, $order) {
    $db = DB::getInstance();
    $db->order_create_update($token, $order);
}

function test_get_all_orders() {
    $db = DB::getInstance();
    $orders = $db->all_orders();
    error_log("All orders: " . json_encode($orders));
}

function test_get_orders_for_token($token) {
    $db = DB::getInstance();
    $orders = $db->orders_for_user($token);
    error_log("Users orders: " . json_encode($orders));
}

function test_update_order_status($token, $orderId, $status) {
    $db = DB::getInstance();
    $db->set_order_status($token, $orderId, $status);
}

$db = DB::getInstance();
$product1 = "{\"0\":\"1\",\"1\":\"Avto\",\"2\":\"Rdeči, Lep\",\"3\":\"5000\",\"4\":\"0\",\"product_id\":\"1\",\"name\":\"Avto\",\"description\":\"Rdeči, Lep\",\"price\":\"5000\",\"active\":\"0\"}";
$product2 = "{\"0\":\"1\",\"1\":\"Kojn\",\"2\":\"Mali, Lep\",\"3\":\"1000\",\"4\":\"0\",\"product_id\":\"2\",\"name\":\"Kojn\",\"description\":\"Kul, Lep\",\"price\":\"2000\",\"active\":\"0\"}";
$db->create_update_product(json_decode($product1, true));
$db->create_update_product(json_decode($product2, true));

$costumer1 = "{\"email\":\"costumer1@h.com\",\"name\":\"Costumer1\",\"lastname\":\"Super\",\"address\":\"BlaBla\",\"phone\":\"123123123\",\"password\":\"123\"}";
$costumer2 = "{\"email\":\"costumer2@h.com\",\"name\":\"Costumer2\",\"lastname\":\"Bad\",\"address\":\"BlaBla\",\"phone\":\"fff\",\"password\":\"123\"}";
$willBeSeller = "{\"email\":\"seller1@h.com\",\"name\":\"Small\",\"lastname\":\"Selr\",\"password\":\"123\"}";
$willBeAdmin = "{\"email\":\"admin@h.com\",\"name\":\"Vse\",\"lastname\":\"Pravice\",\"address\":\"sdaf\",\"phone\":\"fff\",\"password\":\"123\"}";
register_user(json_decode($costumer1, true));
register_user(json_decode($costumer2, true));
register_user(json_decode($willBeAdmin, true));



//test_user_login("2@1.com", "123");

//test_user_by_token("5699616ede16e");
//test_user_logout("56995fc70cc66");

//$order1 = "[{\"item\":{\"0\":\"1\",\"1\":\"Kolondont\",\"2\":\"Univerzal\",\"3\":null,\"4\":\"10\",\"5\":\"1\",\"product_id\":\"1\",\"name\":\"Kolondont\",\"description\":\"Univerzal\",\"image\":null,\"price\":\"10\",\"active\":\"1\"},\"number\":14},{\"item\":{\"0\":\"2\",\"1\":\"Zobna krtacka\",\"2\":\"Ugodno\",\"3\":null,\"4\":\"15\",\"5\":\"1\",\"product_id\":\"2\",\"name\":\"Zobna krtacka\",\"description\":\"Ugodno\",\"image\":null,\"price\":\"15\",\"active\":\"1\"},\"number\":8}]";
//$order2 = "[{\"item\":{\"0\":\"1\",\"1\":\"Kolondont\",\"2\":\"Univerzal\",\"3\":null,\"4\":\"10\",\"5\":\"1\",\"product_id\":\"1\",\"name\":\"Kolondont\",\"description\":\"Univerzal\",\"image\":null,\"price\":\"10\",\"active\":\"1\"},\"number\":10014},{\"item\":{\"0\":\"2\",\"1\":\"Zobna krtacka\",\"2\":\"Ugodno\",\"3\":null,\"4\":\"15\",\"5\":\"1\",\"product_id\":\"2\",\"name\":\"Zobna krtacka\",\"description\":\"Ugodno\",\"image\":null,\"price\":\"15\",\"active\":\"1\"},\"number\":8}]";
//test_place_update_order("56999ece3c574", json_decode($order2));

//test_get_all_orders();

//test_get_orders_for_token("5699920198f4d");