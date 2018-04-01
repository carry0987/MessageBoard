SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/* create_article */
CREATE TABLE IF NOT EXISTS article (
  id int(11) UNSIGNED NOT NULL,
  username varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  title varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  content varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  board_id int(3) NOT NULL,
  sort_id int(3) NOT NULL,
  date datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create reply */
CREATE TABLE IF NOT EXISTS reply (
  id int(11) UNSIGNED NOT NULL,
  username varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  content varchar(100000) COLLATE utf8mb4_unicode_ci NOT NULL,
  article_id varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  board_id int(3) NOT NULL,
  date datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create user */
CREATE TABLE IF NOT EXISTS user (
  id int(11) UNSIGNED NOT NULL,
  username varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  password varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  email varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  is_admin varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  date datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create board */
CREATE TABLE IF NOT EXISTS board (
  id int(3) UNSIGNED NOT NULL,
  board_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  board_description varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  sort_id int(3) NOT NULL,
  date datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create sort */
CREATE TABLE IF NOT EXISTS sort (
  id int(3) UNSIGNED NOT NULL,
  sort_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  sort_description varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  date datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* create config */
CREATE TABLE IF NOT EXISTS config (
  id int(1) UNSIGNED NOT NULL,
  web_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  web_description varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  recaptcha_site varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  recaptcha_secret varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  session_id varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `article`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `board`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sort`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `article`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `board`
  MODIFY `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `config`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `reply`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `sort`
  MODIFY `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;