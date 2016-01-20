<?php

class Queries
{

    // Products
    public static $all_products = "SELECT * FROM shop.products";
    public static $product_by_id = "SELECT * FROM shop.products where product_id = :product_id";
    public static $create_update_product =
        "INSERT INTO products (product_id, name, description, price, active)
         VALUES (:product_id, :name, :description, :price, :active)
         ON DUPLICATE KEY UPDATE name=:name, description=:description, price=:price, active=:active";
    public static $delete_product = "delete from products where product_id = :product_id";

    // Orders
    public static $update_order_status = "UPDATE orders set status=:status, date_modified=CURRENT_TIMESTAMP(), seller_id=:seller_id where order_id = :order_id";

    // Users
    public static $user_by_mail = "SELECT * FROM users WHERE email = :email";
    public static $user_by_token = "SELECT * FROM tokens t, users u WHERE t.user_id = u.user_id and t.token = :token";
    public static $add_token = "INSERT INTO tokens (token, user_id) VALUES (:token, :user_id)";
    public static $delete_token = "delete from tokens where token = :token";
    public static $register_update_user = "
        INSERT INTO users (user_id, name, lastname, email, password, kind, address, phone, active)
        VALUES (:user_id, :name, :lastname, :email, COALESCE(:password, password), :kind, :address, :phone, :active)
        ON DUPLICATE KEY UPDATE name=:name, lastname=:lastname, email=:email,
                                password=COALESCE(:password, password), kind=:kind, address=:address,
                                phone=:phone, active=:active";
    public static $place_update_order =
        " INSERT INTO orders (order_id, costumer_id, seller_id, status, date_modified)
          VALUES (:order_id, :costumer_id, :seller_id, :status, :date_modified)
          ON DUPLICATE KEY UPDATE costumer_id = :costumer_id, seller_id = :seller_id, status = :status,
                                  date_modified = :date_modified";
    public static $place_update_order_product_link =
        "INSERT INTO shop.order_products (order_id, product_id, number)
         VALUES (:order_id, :product_id, :number)
         ON DUPLICATE KEY UPDATE order_id = :order_id, product_id = :product_id";
    public static $all_orders = "SELECT *
          FROM shop.orders o, shop.users u, shop.order_products op, shop.products p
          WHERE o.costumer_id = u.user_id AND p.product_id = op.product_id AND o.order_id = op.order_id";
    public static $users_by_kind = "SELECT * FROM shop.users u WHERE u.kind = :kind";
    public static $change_user_active = "UPDATE users set active=:active where user_id = :user_id";


}
