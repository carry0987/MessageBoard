<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../function/session.php';
ob_start();
require dirname(__FILE__).'/header_install.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Installing';
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

$web_name = input_safety($_POST['web_name']);
$user_email = input_safety($_POST['email']);
$admin_username = input_safety($_POST['admin_username']);
$admin_password = password_hash(input_safety($_POST['admin_password']), PASSWORD_DEFAULT);
$recaptcha_site = input_safety($_POST['recaptcha_site']);
$recaptcha_secret = input_safety($_POST['recaptcha_secret']);
$get_time = date('Y-m-d H:i:s');
$get_url = $_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']));

$insert_demo_article = '
INSERT INTO article (id, username, title, content, board_id, sort_id, date) 
VALUES (\'1\', \''.$admin_username.'\', \'Welcome\', \'Welcome To '.$web_name.' !\', \'1\', \'1\', \''.$get_time.'\')';

$insert_demo_user = '
INSERT INTO user (id, username, password, email, is_admin, date) 
VALUES (\'1\', \''.$admin_username.'\', \''.$admin_password.'\', \''.$user_email.'\', \'1\', \''.$get_time.'\')';

$insert_board = '
INSERT INTO board (id, board_name, board_description, sort_id, date) 
VALUES (\'1\', \'Default\', \'This MessageBoard was made by carry0987\', \'1\', \''.$get_time.'\')';

$insert_sort = '
INSERT INTO sort (id, sort_name, sort_description, date) 
VALUES (\'1\', \'Default Sort\', \'Default Sort\', \''.$get_time.'\')';

$insert_config = '
INSERT INTO config (id, web_name, web_description, site_path, recaptcha_site, recaptcha_secret, session_id) 
VALUES (
\'1\',
 \''.$web_name.'\',
  \'This MessageBoard was made by carry0987\',
   \''.$get_url.'\',
    \''.$recaptcha_site.'\',
     \''.$recaptcha_secret.'\',
      \''.$session_id.'\'
    )';

if(!empty($admin_username) && !empty($admin_password) && !empty($user_email) && !empty($web_name) && !empty($recaptcha_site) && !empty($recaptcha_secret)) {
  $sql = file_get_contents('data.sql');
  $array = explode(';', $sql);
  foreach ($array as $value) {
      $con->query($value.';');
  }
} else {
  echo '<script>';
  echo 'alert("'.$lang_install_empty.'");location.href="../";';
  echo '</script>';
}

$check_table_article_exists = 'SELECT id FROM article';
$check_table_user_exists = 'SELECT id FROM user';
$check_table_board_exists = 'SELECT id FROM board';
$check_table_sort_exists = 'SELECT id FROM sort';
$check_table_config_exists = 'SELECT id,session_id FROM config';
$if_article_exists = $con->query($check_table_article_exists);
$if_user_exists = $con->query($check_table_user_exists);
$if_board_exists = $con->query($check_table_board_exists);
$if_sort_exists = $con->query($check_table_sort_exists);
$if_config_exists = $con->query($check_table_config_exists);

$check_session_id_exists = 'SELECT session_id FROM config WHERE session_id IS NOT NULL';
$if_session_id_exists = $con->query($check_session_id_exists);

if($if_article_exists && $if_user_exists && $if_board_exists && $if_sort_exists && $if_config_exists) {
if($if_article_exists->num_rows > 0 && $if_user_exists->num_rows > 0 && $if_board_exists->num_rows > 0 && $if_sort_exists->num_rows > 0 && $if_config_exists->num_rows > 0) {
  header('Location: ../');
} elseif(
  $if_article_exists->num_rows == 0 && 
  $if_user_exists->num_rows == 0 && 
  $if_sort_exists->num_rows == 0 && 
  $if_config_exists->num_rows == 0 && 
  $if_board_exists->num_rows == 0) {
  $con->query($insert_demo_article);
  $con->query($insert_demo_user);
  $con->query($insert_board);
  $con->query($insert_sort);
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
