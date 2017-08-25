<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../admin/session.php';
ob_start();
require dirname(__FILE__).'/header_install.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Installing';
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

$web_name = input_safety($_POST['web_name']);
$web_email = input_safety($_POST['web_email']);
$admin_username = input_safety($_POST['admin_username']);
$admin_password = input_safety($_POST['admin_password']);

$set_first = '
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00"';

$get_time = date('Y-m-d H:i:s');

$create_msg = '
CREATE TABLE IF NOT EXISTS msg (
  id int(11) UNSIGNED NOT NULL,
  username varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  title varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  content varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  date datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

$create_user = '
CREATE TABLE IF NOT EXISTS user (
  id int(11) UNSIGNED NOT NULL,
  username varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  password varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  is_admin varchar(1) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

$create_config = '
CREATE TABLE IF NOT EXISTS config (
  id int(1) UNSIGNED NOT NULL,
  web_name varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  web_description varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  web_email varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  session_id varchar(16) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

$add_msg_pk  = 'ALTER TABLE msg ADD PRIMARY KEY (id)';
$add_user_pk  = 'ALTER TABLE user ADD PRIMARY KEY (id)';
$add_config_pk  = 'ALTER TABLE config ADD PRIMARY KEY (id)';

$set_msg_ai = 'ALTER TABLE msg MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT';
$set_user_ai = 'ALTER TABLE user MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT';
$set_config_ai = 'ALTER TABLE config MODIFY id int(1) UNSIGNED NOT NULL AUTO_INCREMENT';

$insert_demo_msg = '
INSERT INTO msg (id, username, title, content, date) 
VALUES (NULL'.','."\"".$admin_username."\"".','. "\"Welcome\"".','."\"Welcome To ".$web_name." !\"".','."\"".$get_time."\"".')';

$insert_demo_user = '
INSERT INTO user (id, username, password, is_admin) 
VALUES (NULL, \''.$admin_username.'\', \''.$admin_password.'\', \'1\')';

$insert_config = '
INSERT INTO config (id, web_name, web_description, web_email, session_id) 
VALUES (NULL, \''.$web_name.'\', \'This MessageBoard was made by carry0987\', \''.$web_email.'\', \''.$session_id.'\')';

if(!empty($_POST['admin_username']) && !empty($_POST['admin_password']) && !empty($_POST['web_email']) && !empty($_POST['web_name'])) {
  $con->query($set_first);
  $con->query($create_msg);
  $con->query($create_user);
  $con->query($create_config);
  $con->query($add_msg_pk);
  $con->query($add_user_pk);
  $con->query($add_config_pk);
  $con->query($set_msg_ai);
  $con->query($set_user_ai);
  $con->query($set_config_ai);
} else {
  echo '<script>';
  echo 'alert("'.$lang_install_empty.'");location.href="../";';
  echo '</script>';
}

$check_table_msg_exists = 'SELECT id FROM msg';
$check_table_user_exists = 'SELECT id FROM user';
$check_table_config_exists = 'SELECT id,session_id FROM config';
$if_msg_exists = $con->query($check_table_msg_exists);
$if_user_exists = $con->query($check_table_user_exists);
$if_config_exists = $con->query($check_table_config_exists);

$check_session_id_exists = 'SELECT session_id FROM config WHERE session_id IS NOT NULL';
$if_session_id_exists = $con->query($check_session_id_exists);

if($if_msg_exists && $if_user_exists && $if_config_exists) {
if($if_msg_exists->num_rows > 0 && $if_user_exists->num_rows > 0 && $if_config_exists->num_rows > 0) {
  header('Location: ../');
} elseif($if_msg_exists->num_rows == 0 && $if_user_exists->num_rows == 0 && $if_config_exists->num_rows == 0) {
  $con->query($insert_demo_msg);
  $con->query($insert_demo_user);
  $con->query($insert_config);
  if($if_session_id_exists) {
  if($if_session_id_exists->num_rows > 0) {
    $row = $if_session_id_exists->fetch_assoc();
    $session_id = trim($row['session_id']);
    $session_id = preg_replace('/\s(?=)/', '', $session_id);
  } elseif(!empty($session_id)) {
    echo '<script>';
    echo 'alert("'.$lang_install_success.'");location.href="../login.php";';
    echo '</script>';
  }
  } else {
    echo '<h1>'.$lang_session_error.'</h1>';
    echo '<br />';
    echo '<h2>'.$lang_please.' <a href="../install" style="color: blue;">'.$lang_reinstall.'</a> '.$lang_messageboard.' !</h2>';
}
}
} else {
  echo '<h1>'.$lang_system_error.'</h1>'."\n";
  echo '<h2>'.$lang_please.' <a href="../install" style="color: blue;">'.$lang_reinstall.'</a> '.$lang_messageboard.' !</h2>'."\n";
}
?>