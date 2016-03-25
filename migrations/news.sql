DROP DATABASE IF EXISTS `vk_news`;
CREATE DATABASE `vk_news`
  CHARACTER SET utf8;
CREATE TABLE `vk_news`.`user`
(
  `id`           INT PRIMARY KEY AUTO_INCREMENT,
  `name`         VARCHAR(255),
  `email`        VARCHAR(255) NOT NULL,
  `password`     VARCHAR(255),
  `auth_key`     VARCHAR(255),
  `access_token` VARCHAR(255),
  `verified`     BOOLEAN         DEFAULT FALSE
);
CREATE UNIQUE INDEX `user_email_uindex` ON `vk_news`.`user` (`email`);

USE `vk_news`;

CREATE TABLE `vk_news`.`article`
(
  `id`         INT PRIMARY KEY AUTO_INCREMENT,
  `title`      VARCHAR(255) NOT NULL,
  `content`    TEXT,
  `photo`      VARCHAR(255),
  `author_id`  INT,
  `created_at` TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `article_user_id_fk` FOREIGN KEY (author_id) REFERENCES user (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE `vk_news`.`verification`
(
  `id`         INT PRIMARY KEY AUTO_INCREMENT,
  `user_id`    INT,
  `key`        VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `verification_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES user (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
CREATE UNIQUE INDEX `verification_user_id_uindex` ON `vk_news`.`verification` (`user_id`);