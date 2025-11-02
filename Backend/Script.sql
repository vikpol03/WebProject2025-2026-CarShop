


-- USERS
CREATE TABLE user (
  user_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email          VARCHAR(255) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  full_name      VARCHAR(255) NOT NULL,
  created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_email (email)
) ENGINE=InnoDB;

-- BRAND
CREATE TABLE brand (
  brand_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  country    VARCHAR(120) NULL,
  UNIQUE KEY uq_brand_name (name)
) ENGINE=InnoDB;

-- MODEL
CREATE TABLE model (
  model_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  brand_id   INT UNSIGNED NOT NULL,
  name       VARCHAR(150) NOT NULL,
  year_from  SMALLINT UNSIGNED NULL,
  year_to    SMALLINT UNSIGNED NULL,
  CONSTRAINT fk_model_brand
    FOREIGN KEY (brand_id) REFERENCES brand(brand_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY uq_model_brand_name (brand_id, name)
) ENGINE=InnoDB;

-- CAR
CREATE TABLE car (
  car_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  model_id      INT UNSIGNED NOT NULL,
  vin           VARCHAR(32)  NOT NULL,
  trim          VARCHAR(120) NULL,
  color         VARCHAR(80)  NULL,
  transmission  ENUM('manual','automatic','cvt','other') NULL,
  mileage_km    INT UNSIGNED DEFAULT 0,
  price         DECIMAL(12,2) NOT NULL,
  in_stock      TINYINT(1) NOT NULL DEFAULT 1,
  listed_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_car_model
    FOREIGN KEY (model_id) REFERENCES model(model_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY uq_car_vin (vin),
  KEY idx_car_model (model_id),
  KEY idx_car_instock (in_stock)
) ENGINE=InnoDB;

-- ORDERS
CREATE TABLE orders (
  order_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  order_date    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status        ENUM('pending','paid','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  total_amount  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  KEY idx_orders_user (user_id),
  KEY idx_orders_date (order_date)
) ENGINE=InnoDB;

-- ORDER ITEM
CREATE TABLE order_item (
  order_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id      INT UNSIGNED NOT NULL,
  car_id        INT UNSIGNED NOT NULL,
  quantity      INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price    DECIMAL(12,2) NOT NULL,
  line_total    DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_orderitem_order
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_orderitem_car
    FOREIGN KEY (car_id) REFERENCES car(car_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  KEY idx_orderitem_order (order_id),
  KEY idx_orderitem_car (car_id),
  CONSTRAINT chk_qty CHECK (quantity > 0),
  CONSTRAINT chk_line_total CHECK (line_total = quantity * unit_price)
) ENGINE=InnoDB;

-- IMAGE
CREATE TABLE image (
  image_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  car_id      INT UNSIGNED NOT NULL,
  url         VARCHAR(1024)  NOT NULL,
  sort_order  INT UNSIGNED   NOT NULL DEFAULT 1,
  CONSTRAINT fk_image_car
    FOREIGN KEY (car_id) REFERENCES car(car_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_image_car (car_id),
  KEY idx_image_sort (car_id, sort_order)
) ENGINE=InnoDB;

-- REVIEW
CREATE TABLE review (
  review_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  car_id      INT UNSIGNED NOT NULL,
  rating      TINYINT UNSIGNED NOT NULL,
  comment     TEXT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_review_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_review_car
    FOREIGN KEY (car_id) REFERENCES car(car_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
  UNIQUE KEY uq_review_user_car (user_id, car_id),
  KEY idx_review_car (car_id)
) ENGINE=InnoDB;




