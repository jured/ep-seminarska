
CREATE DATABASE shop
  CHARSET = utf8
  COLLATE = utf8_general_ci;
;

USE shop;

CREATE TABLE shop.products
(
  product_id  INT          NOT NULL  UNIQUE AUTO_INCREMENT,
  name        VARCHAR(255) NOT NULL,
  description TEXT         NULL,
  price       DECIMAL      NOT NULL,
  active      BOOLEAN      NOT NULL         DEFAULT TRUE,
  PRIMARY KEY (product_id)
);


CREATE TABLE shop.orders
(
  order_id       INT                                        NOT NULL  UNIQUE AUTO_INCREMENT,
  costumer_id    INT                                        NOT NULL,
  seller_id      INT                                        NULL,
  status         ENUM ('received', 'confirmed', 'canceled') NOT NULL         DEFAULT 'received',
  date_modified  TIMESTAMP                                  NOT NULL         DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (order_id),
  FOREIGN KEY (costumer_id) REFERENCES users (user_id),
  FOREIGN KEY (seller_id) REFERENCES users (user_id)

);

CREATE TABLE shop.order_products
(
  order_product_id INT NOT NULL  UNIQUE AUTO_INCREMENT,
  order_id         INT NOT NULL,
  product_id       INT NULL,
  number           INT NOT NULL,
  PRIMARY KEY (order_product_id),
  FOREIGN KEY (order_id) REFERENCES orders (order_id)
    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products (product_id)
    ON DELETE SET NULL
);


CREATE TABLE shop.users
(
  user_id  INT                                 NOT NULL  UNIQUE AUTO_INCREMENT,
  name     VARCHAR(255)                        NOT NULL,
  lastname VARCHAR(255)                        NOT NULL,
  email    VARCHAR(255)                        NOT NULL UNIQUE,
  password VARCHAR(255)                        NOT NULL,
  kind     ENUM('admin', 'seller', 'costumer') NOT NULL         DEFAULT 'costumer',
  address  VARCHAR(255)                        NULL,
  phone    VARCHAR(255)                        NULL,
  active   BOOLEAN                             NOT NULL         DEFAULT TRUE,
  PRIMARY KEY (user_id)

);

CREATE TABLE shop.tokens
(
  token   VARCHAR(255) NOT NULL UNIQUE,
  user_id INT          NOT NULL,
  created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users (user_id)
);





