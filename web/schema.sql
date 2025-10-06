DROP
DATABASE IF EXISTS taskforce;

CREATE
DATABASE taskforce
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE
taskforce;

CREATE TABLE cities
(
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(128)   NOT NULL UNIQUE,
    latitude   DECIMAL(11, 8) NOT NULL,
    longitude  DECIMAL(11, 8) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories
(
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL UNIQUE,
    icon VARCHAR(128) NOT NULL
);

CREATE TABLE users
(
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(128)       NOT NULL,
    email           VARCHAR(128)       NOT NULL UNIQUE,
    password        VARCHAR(128)       NOT NULL,
    role            ENUM('customer', 'executor') NOT NULL,
    birthday        DATETIME,
    phone           CHAR(11),
    telegram        VARCHAR(128),
    info            TEXT,
    specializations VARCHAR(255),
    avatar          VARCHAR(255),
    succesful_tasks INT UNSIGNED,
    failed_tasks    INT UNSIGNED,
    city_id         INT UNSIGNED NOT NULL,
    vk_id           INT UNSIGNED,
    hidden_contacts INT UNSIGNED DEFAULT 0 NOT NULL,
    total_score     FLOAT    DEFAULT 0 NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities (id)
);

CREATE TABLE tasks
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT UNSIGNED NOT NULL,
    budget      VARCHAR(128),
    status      VARCHAR(128) NOT NULL DEFAULT 'new',
    city_id     INT UNSIGNED NOT NULL,
    location    VARCHAR(255),
    latitude    DECIMAL(11, 8),
    longitude   DECIMAL(11, 8),
    deadline    DATETIME,
    customer_id INT UNSIGNED NOT NULL,
    executor_id INT UNSIGNED,
    created_at  DATETIME              DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    status      VARCHAR(128) NOT NULL DEFAULT 'new',
    created_at  DATETIME              DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks (id),
    FOREIGN KEY (executor_id) REFERENCES users (id)
);

CREATE TABLE reviews
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id     INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    executor_id INT UNSIGNED NOT NULL,
    rating      INT UNSIGNED,
    comment     TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks (id),
    FOREIGN KEY (customer_id) REFERENCES users (id),
    FOREIGN KEY (executor_id) REFERENCES users (id)
);

CREATE TABLE files
(
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id    INT UNSIGNED NOT NULL,
    path       VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks (id)
);

CREATE
FULLTEXT INDEX task_title_search ON tasks(title);
CREATE
FULLTEXT INDEX task_description_search ON tasks(description);
