SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/* create user */
CREATE TABLE IF NOT EXISTS user (
  id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  username varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  password varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  email varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  is_admin varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  last_login datetime NOT NULL,
  join_date datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create_article */
CREATE TABLE IF NOT EXISTS article (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  title varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  content varchar(10000) COLLATE utf8mb4_unicode_ci NOT NULL,
  board_id int(3) UNSIGNED NOT NULL,
  set_sitemap varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  last_edit datetime NOT NULL,
  post_date datetime NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`, `board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create board */
CREATE TABLE IF NOT EXISTS board (
  id int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  board_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  board_description varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  category_id int(3) UNSIGNED NOT NULL,
  set_sitemap varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  create_date datetime NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create category */
CREATE TABLE IF NOT EXISTS category (
  id int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  category_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  category_description varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  set_sitemap varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  create_date datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create reply */
CREATE TABLE IF NOT EXISTS reply (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  content varchar(100000) COLLATE utf8mb4_unicode_ci NOT NULL,
  reply_to int(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  board_id int(3) UNSIGNED NOT NULL,
  reply_date datetime NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`, `board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create comment */
CREATE TABLE IF NOT EXISTS comment (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  content varchar(100000) COLLATE utf8mb4_unicode_ci NOT NULL,
  comment_to int(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  comment_date datetime NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create config */
CREATE TABLE IF NOT EXISTS config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  web_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  web_description varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create recaptcha */
CREATE TABLE IF NOT EXISTS recaptcha (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  site_key varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  secret_key varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Foreign key */
ALTER TABLE `article` ADD CONSTRAINT `Article_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `article` ADD CONSTRAINT `Article_Board` FOREIGN KEY (`board_id`) REFERENCES `board`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `board` ADD CONSTRAINT `Board_Category` FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `reply` ADD CONSTRAINT `Reply_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `reply` ADD CONSTRAINT `Reply_Board` FOREIGN KEY (`board_id`) REFERENCES `board`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `comment` ADD CONSTRAINT `Comment_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
