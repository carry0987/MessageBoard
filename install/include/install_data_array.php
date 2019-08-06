<?php
if (defined('IN_INSTALL') !== true) {
    exit('Access Denied');
}
//Set value
$insert[0] = '1';
$insert[1] = 'Welcome';
$insert[2] = 'Welcome To Messageboard !';
$insert[3] = 'true';
$insert[4] = 'Default';
$insert[5] = 'This MessageBoard was made by carry0987';
$insert[6] = 'Default category';
$insert[7] = 'This is default category';
$insert[8] = 'Messageboard';
$insert[9] = '0';
$insert[10] = 'localhost';
$insert[11] = 'simple_captcha';
$insert[12] = time();

//Email domain config
$domain['allow'] = 'gmail.com|yahoo.com|yahoo.com.tw|icloud.com|outlook.com';
$domain['disallow'] = '';

//Localhost email config
$hostUrl = str_replace('www.', '', $_SERVER['HTTP_HOST']);

//SMTP Config
$smtp['host'] = 'smtp.gmail.com';
$smtp['user'] = 'example@gmail.com';
$smtp['pw'] = '';
$smtp['from'] = 'example@gmail.com';
$smtp['name'] = 'Admin';

//Captcha Apply Config
$apply_page = array('signup', 'login');
$captcha_apply = serialize($apply_page);

//Simple Captcha Config
$captcha['id'] = 1;
$captcha['image_height'] = 60;
$captcha['image_width'] = 250;
$captcha['font_file'] = 'monofont.ttf';
$captcha['text_color'] = '#142864';
$captcha['noise_color'] = '#142864';
$captcha['total_character'] = 6;
$captcha['random_dots'] = 50;
$captcha['random_lines'] = 25;
$captcha['check_sensitive'] = 0;

//Insert default value
$user_query = 'INSERT INTO user (display_name, username, password, email, language, is_admin, online_status, last_login, join_date) VALUES (?,?,?,?,?,?,?,?,?)';
$category_query = 'INSERT INTO category (name, description, set_sitemap, last_edit, create_date) VALUES (?,?,?,?,?)';
$board_query = 'INSERT INTO board (name, description, category_id, set_sitemap, last_edit, create_date) VALUES (?,?,?,?,?,?)';
$email_config_query = 'INSERT INTO email_config (id, enable, type, allow_domain, disallow_domain) VALUES (?,?,?,?,?)';
$global_config_query = 'INSERT INTO global_config (id, web_name, web_description, web_language, web_timezone, last_edit) VALUES (?,?,?,?,?,?)';

$config_query = array(
    array(
        'table_name' => 'upload_config', 
        'config' => 'INSERT INTO upload_config (id, enable, type, thumbnail_height, thumbnail_width) VALUES (?,?,?,?,?)', 
        'prepare' => 'iisii', 
        'bind' => array($insert[0], $insert[0], 'local', 0, 200)
    ),
    array(
        'table_name' => 'upload_local', 
        'config' => 'INSERT INTO upload_local (id, local_dir, local_url, allowed_ext, disallowed_ext, max_size) VALUES (?,?,?,?,?,?)', 
        'prepare' => 'issssi', 
        'bind' => array($insert[0], './data/attachment', 'data/attachment', '', '', 1024)
    ),
    array(
        'table_name' => 'upload_remote', 
        'config' => 'INSERT INTO upload_remote (id, ftp_host, ftp_user, ftp_pw, remote_dir, remote_url, allowed_ext, disallowed_ext, max_size) VALUES (?,?,?,?,?,?,?,?,?)', 
        'prepare' => 'isssssssi', 
        'bind' => array($insert[0], '', '', '', '', '', '', '', 1024)
    ),
    array(
        'table_name' => 'seo_sitemap_config', 
        'config' => 'INSERT INTO seo_sitemap_config (id, enable, auto_update, sitemap_path) VALUES (?,?,?,?)', 
        'prepare' => 'iiis', 
        'bind' => array($insert[0], $insert[0], $insert[0], 'sitemap.xml')
    ),
    array(
        'table_name' => 'email_smtp', 
        'config' => 'INSERT INTO email_smtp (id, smtp_host, smtp_user, smtp_pw, send_from, send_name) VALUES (?,?,?,?,?,?)', 
        'prepare' => 'isssss', 
        'bind' => array($insert[0], $smtp['host'], $smtp['user'], $smtp['pw'], $smtp['from'], $smtp['name'])
    ),
    array(
        'table_name' => 'email_localhost', 
        'config' => 'INSERT INTO email_localhost (id, send_from, send_name) VALUES (?,?,?)', 
        'prepare' => 'iss', 
        'bind' => array($insert[0], 'admin@'.$hostUrl, $insert[8])
    ),
    array(
        'table_name' => 'captcha_config', 
        'config' => 'INSERT INTO captcha_config (id, type, apply) VALUES (?,?,?)', 
        'prepare' => 'iss', 
        'bind' => array($insert[0], $insert[11], $captcha_apply)
    ),
    array(
        'table_name' => 'simple_captcha', 
        'config' => 'INSERT INTO simple_captcha (id, image_height, image_width, font_file, text_color, noise_color, total_character, random_dots, random_lines, check_sensitive) VALUES (?,?,?,?,?,?,?,?,?,?)', 
        'prepare' => 'iiisssiiii', 
        'bind' => array($captcha['id'], $captcha['image_height'], $captcha['image_width'], $captcha['font_file'], $captcha['text_color'], $captcha['noise_color'], $captcha['total_character'], $captcha['random_dots'], $captcha['random_lines'], $captcha['check_sensitive'])
    ),
    array(
        'table_name' => 'google_recaptcha', 
        'config' => 'INSERT INTO google_recaptcha (id) VALUES (?)', 
        'prepare' => 'i', 
        'bind' => array($insert[0])
    ),
    array(
        'table_name' => 'svg_captcha', 
        'config' => 'INSERT INTO svg_captcha (id, image_height, image_width, total_character, difficulty) VALUES (?,?,?,?,?)', 
        'prepare' => 'iiiii', 
        'bind' => array($captcha['id'], 100, 250, 4, 0)
    ),
    array(
        'table_name' => 'social_login_config', 
        'config' => 'INSERT INTO social_login_config (id, enable, type) VALUES (?,?,?)', 
        'prepare' => 'iis', 
        'bind' => array($insert[0], $insert[9], 'github_login')
    ),
    array(
        'table_name' => 'github_login', 
        'config' => 'INSERT INTO github_login (id, client_id, client_secret, redirect_url) VALUES (?,?,?,?)', 
        'prepare' => 'isss', 
        'bind' => array($insert[0], '', '', '')
    )
);
