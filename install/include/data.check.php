<?php
if (defined('IN_INSTALL') === false) {
    exit('Access Denied');
}
require dirname(__FILE__).'/inc.language.php';
require dirname(__FILE__).'/install_function.php';
//Get url
$url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$redirect_url = dirname(dirname($url));

if (isset($_POST['db_host'])) {
    $db_host = input_filter($_POST['db_host']);
} else {
    echo $LANG['common']['input_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['db_name'])) {
    $db_name = input_filter($_POST['db_name']);
} else {
    echo $LANG['common']['input_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['db_port'])) {
    $db_port = input_filter($_POST['db_port']);
} else {
    echo $LANG['common']['input_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['db_user'])) {
    $db_user = input_filter($_POST['db_user']);
} else {
    echo $LANG['common']['input_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['db_password'])) {
    $db_password = input_filter($_POST['db_password']);
} else {
    echo $LANG['common']['input_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['lang'])) {
    $web_lang = input_filter($_POST['lang']);
} else {
    $web_lang = 'en_US';
}

if (isset($_POST['email_set'])) {
    if ($_POST['email_set'] === 'enable') {
        $get_email_set = 1;
    } else {
        $get_email_set = 0;
    }
} else {
    echo $LANG['common']['input_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['admin_display_name'])) {
    $admin_display_name = input_filter($_POST['admin_display_name']);
} else {
    echo $LANG['common']['display_name_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['admin_username'])) {
    $admin_username = input_filter($_POST['admin_username']);
} else {
    echo $LANG['common']['username_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['admin_password'])) {
    $admin_password = input_filter($_POST['admin_password']);
} else {
    echo $LANG['common']['password_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['admin_psw_confirm'])) {
    $admin_psw_confirm = input_filter($_POST['admin_psw_confirm']);
} else {
    echo $LANG['common']['password_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

if (isset($_POST['email'])) {
    $user_email = input_filter($_POST['email']);
} else {
    echo $LANG['common']['email_empty'];
    echo '<br />';
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
}

$default_language = 'en_US';
$web_timezone = 'Europe/London';
$get_time = time();
$get_path = dirname(dirname(dirname($_SERVER['PHP_SELF'])));
$get_url = $_SERVER['HTTP_HOST'].$get_path;
$email_format = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

function check_regex($strings, $standard)
{
   if (preg_match($standard, $strings)) {
      return 1;
   } else {
      return 0;
   }
}

if ($admin_password !== $admin_psw_confirm) {
    echo $LANG['common']['repassword_error'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($db_host == '') {
    echo $LANG['database']['db_host_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($db_name == '') {
    echo $LANG['database']['db_name_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($db_port == '') {
    echo $LANG['database']['db_port_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($db_user == '') {
    echo $LANG['database']['db_user_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($db_password == '') {
    echo $LANG['database']['db_password_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($admin_display_name == '') {
    echo $LANG['common']['display_name_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($admin_username == '') {
    echo $LANG['common']['username_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($admin_password == '') {
    echo $LANG['common']['password_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($admin_psw_confirm == '') {
    echo $LANG['common']['password_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif ($user_email == '') {
    echo $LANG['common']['email_empty'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} elseif (!check_regex($user_email, $email_format) == 1) {
    echo $LANG['common']['email_format_error'];
    echo '<a href="'.$redirect_url.'">'.$LANG['common']['back_page'].'</a>';
    exit();
} else {
    $set_admin_psw = password_hash($admin_password, PASSWORD_DEFAULT);
    $data_check = true;
}
