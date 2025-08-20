DROP
DATABASE IF EXISTS task_force;

CREATE
DATABASE task_force
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE
task_force;

CREATE TABLE cities
(
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(128)   NOT NULL UNIQUE,
  latitude   DECIMAL(10, 8) NOT NULL,
  longitude  DECIMAL(10, 8) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories
(
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL UNIQUE,
  icon VARCHAR(128)
);

CREATE TABLE users
(
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer', 'executor') NOT NULL,
  city_id       INT UNSIGNED NOT NULL,
  avatar        VARCHAR(255),
  telegram      VARCHAR(255),
  phone         CHAR(11),
  show_contacts BOOLEAN   DEFAULT TRUE,
  birthday      DATE,
  info          TEXT,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (city_id) REFERENCES cities (id)
);

CREATE TABLE user_specializations
(
  user_id     INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, category_id),
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (category_id) REFERENCES categories (id)
);

CREATE TABLE tasks
(
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT         NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  budget      INT UNSIGNED,
  status      ENUM('new', 'work', 'done', 'failed', 'cancelled') NOT NULL DEFAULT 'new',
  city_id     INT UNSIGNED NOT NULL,
  latitude    DECIMAL(10, 8),
  longitude   DECIMAL(10, 8),
  ended_at    DATE,
  customer_id INT UNSIGNED NOT NULL,
  executor_id INT UNSIGNED,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories (id),
  FOREIGN KEY (city_id) REFERENCES cities (id),
  FOREIGN KEY (customer_id) REFERENCES users (id),
  FOREIGN KEY (executor_id) REFERENCES users (id)
);

CREATE TABLE responses
(
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  task_id     INT UNSIGNED NOT NULL,
  executor_id INT UNSIGNED NOT NULL,
  price       INT UNSIGNED,
  comment     TEXT,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks (id),
  FOREIGN KEY (executor_id) REFERENCES users (id)
);

CREATE TABLE reviews
(
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  task_id     INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  executor_id INT UNSIGNED NOT NULL,
  rating      TINYINT UNSIGNED NOT NULL CHECK (rating >= 1 AND rating <= 5),
  comment     TEXT NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks (id),
  FOREIGN KEY (customer_id) REFERENCES users (id),
  FOREIGN KEY (executor_id) REFERENCES users (id)
);

CREATE TABLE files
(
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  task_id    INT UNSIGNED NOT NULL,
  path       VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks (id)
);
