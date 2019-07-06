SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/* Create user */
CREATE TABLE IF NOT EXISTS user (
  uid int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  display_name varchar(20) NOT NULL,
  username varchar(20) NOT NULL,
  password varchar(255) NOT NULL,
  bio varchar(160) NOT NULL,
  two_factor tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  email varchar(100) NOT NULL,
  language varchar(5) NOT NULL,
  is_admin tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  banned tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  online_status int(20) NOT NULL DEFAULT '0',
  last_login int(20) NOT NULL,
  join_date int(20) NOT NULL,
  PRIMARY KEY (`uid`),
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Custom Timezone */
CREATE TABLE IF NOT EXISTS user_timezone (
  id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  timezone varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create remember me */
CREATE TABLE IF NOT EXISTS remember_me (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  selector_hash varchar(16) NOT NULL,
  password_hash varchar(255) NOT NULL,
  expiry_date int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Anonymous */
CREATE TABLE IF NOT EXISTS user_anony (
  id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  anony_name varchar(30) NOT NULL DEFAULT 'Anonymous',
  online_status int(20) NOT NULL,
  last_login int(20) NOT NULL,
  join_date int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Two Factor Authentication */
CREATE TABLE IF NOT EXISTS two_factor_auth (
  id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  auth_uid int(8) UNSIGNED NOT NULL,
  authentication varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`auth_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create forgot identity */
CREATE TABLE IF NOT EXISTS forgot_identity (
  id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  md5_username varchar(100) NOT NULL,
  user_email varchar(100) NOT NULL,
  forgot_pass_identity varchar(32) NOT NULL,
  forgot_timeout int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create article */
CREATE TABLE IF NOT EXISTS article (
  aid int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  title varchar(150) NOT NULL,
  content text(30000) NOT NULL,
  description varchar(500) NOT NULL,
  board_id int(3) UNSIGNED NOT NULL,
  set_sitemap tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  changefreq varchar(8) NOT NULL DEFAULT 'hourly',
  priority char(3) NOT NULL DEFAULT '0.8',
  property tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  last_edit int(20) NOT NULL,
  post_date int(20) NOT NULL,
  PRIMARY KEY (`aid`),
  INDEX (`user_id`, `board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create article pinned */
CREATE TABLE IF NOT EXISTS article_pinned (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id int(11) UNSIGNED NOT NULL,
  pinned_sort int(5) UNSIGNED NOT NULL,
  apply varchar(10000) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create board */
CREATE TABLE IF NOT EXISTS board (
  bid int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  name varchar(150) NOT NULL,
  description varchar(1000) NOT NULL,
  category_id int(3) UNSIGNED NOT NULL,
  set_sitemap tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  changefreq varchar(8) NOT NULL DEFAULT 'weekly',
  priority char(3) NOT NULL DEFAULT '0.6',
  last_edit int(20) NOT NULL,
  create_date int(20) NOT NULL,
  PRIMARY KEY (`bid`),
  INDEX (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create category */
CREATE TABLE IF NOT EXISTS category (
  cid int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  name varchar(150) NOT NULL,
  description varchar(1000) NOT NULL,
  set_sitemap tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  changefreq varchar(8) NOT NULL DEFAULT 'monthly',
  priority char(3) NOT NULL DEFAULT '0.4',
  last_edit int(20) NOT NULL,
  create_date int(20) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create reply */
CREATE TABLE IF NOT EXISTS reply (
  reply_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  content text(30000) NOT NULL,
  article_id int(11) UNSIGNED NOT NULL,
  last_edit int(20) NOT NULL,
  reply_date int(20) NOT NULL,
  PRIMARY KEY (`reply_id`),
  INDEX (`user_id`, `article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create comment */
CREATE TABLE IF NOT EXISTS comment (
  comment_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  content varchar(300) NOT NULL,
  reply_id int(11) UNSIGNED NOT NULL,
  article_id int(11) UNSIGNED NOT NULL,
  last_edit int(20) NOT NULL,
  comment_date int(20) NOT NULL,
  PRIMARY KEY (`comment_id`),
  INDEX (`user_id`, `reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create notification for article */
CREATE TABLE IF NOT EXISTS notif_article (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  notif_to int(8) UNSIGNED NOT NULL,
  notif_from int(8) UNSIGNED NOT NULL,
  article_id int(11) UNSIGNED NOT NULL,
  reply_id int(11) UNSIGNED NOT NULL,
  is_read tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  notif_date int(20) NOT NULL,
  read_date int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`notif_to`, `notif_from`, `reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create notification for reply */
CREATE TABLE IF NOT EXISTS notif_reply (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  notif_to int(8) UNSIGNED NOT NULL,
  notif_from int(8) UNSIGNED NOT NULL,
  article_id int(11) UNSIGNED NOT NULL,
  reply_id int(11) UNSIGNED NOT NULL,
  comment_id int(11) UNSIGNED NOT NULL,
  is_read tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  notif_date int(20) NOT NULL,
  read_date int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`notif_to`, `notif_from`, `comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create global config */
CREATE TABLE IF NOT EXISTS global_config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  web_name varchar(20) NOT NULL,
  web_description varchar(300) NOT NULL,
  web_language varchar(6) NOT NULL DEFAULT 'en_US',
  web_timezone varchar(50) NOT NULL,
  last_edit int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create upload config */
CREATE TABLE IF NOT EXISTS upload_config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  enable tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  type varchar(10) NOT NULL,
  image_library tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  thumbnail_height int(3) UNSIGNED NOT NULL,
  thumbnail_width int(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create local upload config */
CREATE TABLE IF NOT EXISTS upload_local (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  local_dir varchar(250) NOT NULL,
  local_url varchar(250) NOT NULL,
  allowed_ext varchar(300) NOT NULL,
  disallowed_ext varchar(300) NOT NULL,
  max_size int(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create remote upload config */
CREATE TABLE IF NOT EXISTS upload_remote (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  use_ssl tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  ftp_host varchar(250) NOT NULL,
  ftp_port int(5) UNSIGNED NOT NULL DEFAULT '21',
  ftp_user varchar(50) NOT NULL,
  ftp_pw varchar(256) NOT NULL,
  pasv tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  remote_dir varchar(250) NOT NULL,
  remote_url varchar(250) NOT NULL,
  ftp_timeout int(8) UNSIGNED NOT NULL DEFAULT '0',
  allowed_ext varchar(300) NOT NULL,
  disallowed_ext varchar(300) NOT NULL,
  max_size int(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create seo-sitemap config */
CREATE TABLE IF NOT EXISTS seo_sitemap_config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  enable tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  auto_update tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  sitemap_path varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create email config */
CREATE TABLE IF NOT EXISTS email_config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  enable tinyint(1) UNSIGNED NOT NULL,
  type varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create email localhost config */
CREATE TABLE IF NOT EXISTS email_localhost (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  charset char(30) NOT NULL DEFAULT 'utf-8',
  send_from varchar(100) NOT NULL,
  send_name varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create email smtp config */
CREATE TABLE IF NOT EXISTS email_smtp (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  charset char(30) NOT NULL DEFAULT 'utf-8',
  smtp_host varchar(40) NOT NULL,
  smtp_user varchar(30) NOT NULL,
  smtp_pw varchar(30) NOT NULL,
  send_from varchar(100) NOT NULL,
  send_name varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create captcha config */
CREATE TABLE IF NOT EXISTS captcha_config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  enable tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  type varchar(20) NOT NULL DEFAULT 'simple_captcha',
  apply varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create simple captcha config */
CREATE TABLE IF NOT EXISTS simple_captcha (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  image_height int(4) UNSIGNED NOT NULL,
  image_width int(4) UNSIGNED NOT NULL,
  font_file varchar(50) NOT NULL,
  text_color char(8) NOT NULL,
  noise_color char(8) NOT NULL,
  total_character int(2) UNSIGNED NOT NULL DEFAULT '4',
  random_dots int(3) UNSIGNED NOT NULL,
  random_lines int(3) UNSIGNED NOT NULL,
  check_sensitive tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create recaptcha config */
CREATE TABLE IF NOT EXISTS google_recaptcha (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  site_key varchar(60) NOT NULL,
  secret_key varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create svg captcha config */
CREATE TABLE IF NOT EXISTS svg_captcha (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  image_height int(4) UNSIGNED NOT NULL DEFAULT '100',
  image_width int(4) UNSIGNED NOT NULL DEFAULT '250',
  total_character int(2) UNSIGNED NOT NULL DEFAULT '4',
  difficulty int(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create template */
CREATE TABLE IF NOT EXISTS template (
  tpl_id int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  tpl_path varchar(60) NOT NULL,
  tpl_name varchar(60) NOT NULL,
  tpl_type varchar(4) NOT NULL,
  tpl_md5 varchar(80) NOT NULL,
  tpl_expire_time int(20) NOT NULL,
  tpl_verhash varchar(20) NOT NULL,
  PRIMARY KEY (`tpl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create social login config */
CREATE TABLE IF NOT EXISTS social_login_config (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  enable tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  type varchar(20) NOT NULL DEFAULT 'github_login',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create github login config */
CREATE TABLE IF NOT EXISTS github_login (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  client_id varchar(150) NOT NULL,
  client_secret varchar(150) NOT NULL,
  redirect_url varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create github user */
CREATE TABLE IF NOT EXISTS github_user (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  github_display_name varchar(50) NOT NULL,
  github_username varchar(50) NOT NULL,
  github_email varchar(30) NOT NULL,
  github_bio varchar(160) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create Avatar */
CREATE TABLE IF NOT EXISTS attach_avatar (
  id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(8) UNSIGNED NOT NULL,
  origin_name varchar(300) NOT NULL,
  file_name varchar(30) NOT NULL,
  upload_date int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Create Image */
CREATE TABLE IF NOT EXISTS attach_image (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id int(11) UNSIGNED NOT NULL,
  origin_name varchar(300) NOT NULL,
  file_name varchar(30) NOT NULL,
  upload_date int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Foreign key */
ALTER TABLE `article` ADD CONSTRAINT `Article_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `article` ADD CONSTRAINT `Article_Board` FOREIGN KEY (`board_id`) REFERENCES `board`(`bid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `article_pinned` ADD CONSTRAINT `Article_Pinned` FOREIGN KEY (`article_id`) REFERENCES `article`(`aid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `board` ADD CONSTRAINT `Board_Category` FOREIGN KEY (`category_id`) REFERENCES `category`(`cid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `reply` ADD CONSTRAINT `Reply_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `reply` ADD CONSTRAINT `Reply_Article` FOREIGN KEY (`article_id`) REFERENCES `article`(`aid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `comment` ADD CONSTRAINT `Comment_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `comment` ADD CONSTRAINT `Comment_Reply` FOREIGN KEY (`reply_id`) REFERENCES `reply`(`reply_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `notif_article` ADD CONSTRAINT `Notif_Article_To` FOREIGN KEY (`notif_to`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `notif_article` ADD CONSTRAINT `Notif_Article_From` FOREIGN KEY (`notif_from`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `notif_article` ADD CONSTRAINT `Notif_Reply_ID` FOREIGN KEY (`reply_id`) REFERENCES `reply`(`reply_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `notif_reply` ADD CONSTRAINT `Notif_Reply_TO` FOREIGN KEY (`notif_to`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `notif_reply` ADD CONSTRAINT `Notif_Reply_From` FOREIGN KEY (`notif_from`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `notif_reply` ADD CONSTRAINT `Notif_Comment_ID` FOREIGN KEY (`comment_id`) REFERENCES `comment`(`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `two_factor_auth` ADD CONSTRAINT `Two_Factor_Auth_User` FOREIGN KEY (`auth_uid`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `forgot_identity` ADD CONSTRAINT `Forgot_User` FOREIGN KEY (`user_email`) REFERENCES `user`(`email`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `github_user` ADD CONSTRAINT `Github_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `remember_me` ADD CONSTRAINT `Remember_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `user_anony` ADD CONSTRAINT `Anonymous_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `user_timezone` ADD CONSTRAINT `Custom_Timezone` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `attach_avatar` ADD CONSTRAINT `User_Avatar` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `attach_image` ADD CONSTRAINT `User_Image` FOREIGN KEY (`article_id`) REFERENCES `article`(`aid`) ON DELETE CASCADE ON UPDATE CASCADE;
