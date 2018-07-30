<?php
require dirname(__FILE__).'/inc.language.php';
require dirname(__FILE__).'/function_filter.php';

if (isset($_POST['db_host'])) {
    $db_host = input_filter($_POST['db_host']);
} else {
    echo $lang_install_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['db_name'])) {
    $db_name = input_filter($_POST['db_name']);
} else {
    echo $lang_install_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['db_port'])) {
    $db_port = input_filter($_POST['db_port']);
} else {
    echo $lang_install_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['db_user'])) {
    $db_user = input_filter($_POST['db_user']);
} else {
    echo $lang_install_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['db_password'])) {
    $db_password = input_filter($_POST['db_password']);
} else {
    echo $lang_install_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['admin_username'])) {
    $admin_username = input_filter($_POST['admin_username']);
} else {
    echo $lang_username_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['admin_password'])) {
    $admin_password = input_filter($_POST['admin_password']);
} else {
    echo $lang_password_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['admin_psw_confirm'])) {
    $admin_psw_confirm = input_filter($_POST['admin_psw_confirm']);
} else {
    echo $lang_password_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['email'])) {
    $user_email = input_filter($_POST['email']);
} else {
    echo $lang_email_empty;
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['recaptcha_site'])) {
    $recaptcha_site = input_filter($_POST['recaptcha_site']);
} else {
    echo 'reCaptcha Site Empty !';
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

if (isset($_POST['recaptcha_secret'])) {
    $recaptcha_secret = input_filter($_POST['recaptcha_secret']);
} else {
    echo 'reCaptcha Secret Empty !';
    echo '<br />';
    echo '<a href="../">Back</a>';
    exit();
}

$get_time = date('Y-m-d H:i:s');
$get_path = dirname(dirname(dirname($_SERVER['PHP_SELF'])));
$get_url = $_SERVER['HTTP_HOST'].$get_path;
$email_format = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

function check_regex($strings, $standard) {
   if(preg_match($standard, $strings)) {
      return 1;
   } else {
      return 0;
   }
}

if ($admin_password !== $admin_psw_confirm) {
    echo $lang_repassword_error;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($db_host == '') {
    echo $lang_db_host_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($db_name == '') {
    echo $lang_db_name_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($db_port == '') {
    echo $lang_db_port_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($db_user == '') {
    echo $lang_db_user_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($db_password == '') {
    echo $lang_db_password_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($admin_username == '') {
    echo $lang_username_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($admin_password == '') {
    echo $lang_password_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($admin_psw_confirm == '') {
    echo $lang_password_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($user_email == '') {
    echo $lang_email_empty;
    echo '<a href="../">Back</a>';
    exit();
} elseif (!check_regex($user_email, $email_format) == 1) {
    echo $lang_email_format_error;
    echo '<a href="../">Back</a>';
    exit();
} elseif ($recaptcha_site == '') {
    echo 'reCaptcha Site Empty !';
    echo '<a href="../">Back</a>';
    exit();
} elseif ($recaptcha_secret == '') {
    echo 'reCaptcha Secret Empty !';
    echo '<a href="../">Back</a>';
    exit();
} else {
    $set_admin_psw = password_hash($admin_password, PASSWORD_DEFAULT);
    $data_check = 'pass';
}
