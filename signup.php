<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
require dirname(__FILE__).'/source/recaptcha/recaptcha.php';
$load = new Load;
$load->loadClass('template', 'recaptchainfo', 'check', 'data_create');
$load->loadFunction('filter');

//Template setting
$options = array(
    'template_dir' => 'template/common/',
    'css_dir' => 'static/css/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/',
    'auto_update' => true,
    'cache_lifetime' => 0,
);

$template = Template::getInstance();
$template->setOptions($options);

//reCaptcha
$recaptcha_setting = new ReCaptchaInfo($conn);
$siteKey = $recaptcha_setting->reCaptchaSite();
$secret = $recaptcha_setting->reCaptchaSecret();
$resp = '';

//Check user exist
$account_check = new Check($conn);
if (!empty($_GET['check_username'])) {
    echo $account_check->checkUsername($_GET['check_username']);
    exit();
} elseif (!empty($_GET['check_email'])) {
    echo $account_check->checkEmail($_GET['check_email']);
    exit();
}

//Check login
if (!empty($login['username'])) {
    header('Location: ./');
} else {
    $signup_permit = true;
    $username_error = $password_error = $email_error = $recaptcha_error = '';
}

if (isset($_POST['submit']) && isset($_POST['g-recaptcha-response'])) {
    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
    if ($resp->isSuccess() != true) {
        echo "
        <script>
            alert(\"$lang_recaptcha_error\");location.href='$base_url/signup.php';\n;
        </script>
        ";
        exit();
    }

    $username = input_filter($_POST['username']);
    $password = input_filter($_POST['password']);
    $password_confirm = input_filter($_POST['pdr']);
    $email = input_filter($_POST['email']);
    $get_time = date('Y-m-d H:i:s');

    //Check username
    if (empty($_POST['username'])) {
        $username_error = $lang_username_empty;
        $signup_permit = false;
    } elseif (!empty($_GET['check_username'])) {
        if ($account_check->checkUsername($_GET['check_username']) == 1) {
            $username_error = $lang_duplicate_username;
            $signup_permit = false;
        }
    }

    //Check password
    if (empty($password)) {
        $password_error = $lang_password_empty;
        $signup_permit = false;
    } elseif (!empty($password) && !empty($password_confirm)) {
        if ($password_confirm !== $password) {
            $password_error = $lang_repassword_error;
            $signup_permit = false;
        }
    }

    //Check email
    if (empty($email)) {
        $email_error = $lang_email_empty;
        $signup_permit = false;
    } elseif (!preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/',$email)) {
        $email_error = $lang_email_format_error;
        $signup_permit = false;
    } elseif (!empty($_GET['check_email'])) {
        if ($account_check->checkEmail($_GET['check_email']) == 1) {
            $email_error = $lang_duplicate_email;
            $signup_permit = false;
        }
    }

    //Submit check
    if ($signup_permit === true && !empty($username) && !empty($password) && !empty($email)) {
        $insert_password = password_hash($password, PASSWORD_DEFAULT);
        if ($account_check->checkUsername($username) == 1) {
                $username_error = $lang_duplicate_username;
                $signup_permit = false;
        } elseif ($account_check->checkEmail($email) == 1) {
                $email_error = $lang_duplicate_email;
                $signup_permit = false;
        }

        //Email authenticate
        $to = $email;
        $subject = $lang_welcome_to.' '.$meta['name'];
        $msg = $lang_dear.' '.$username."\n".$lang_welcome_to.' '.$meta['name'];

        if (!mail("$to", "$subject", "$msg")) {
            $signup_permit = false;
            echo '
                <script>
                    alert("Email System Error !");location.href="'.$base_url.'/signup.php";
                </script>
            ';
            exit();
        }

        if ($signup_permit === true) {
            $user_info = array(
                'username' => $_POST['username'],
                'password' => $insert_password,
                'email' => $email,
                'is_admin' => 'false',
                'last_login' => $get_time,
                'join_date' => $get_time,
            );
            $create_user = new DataCreate($conn);
            $create_user->createUser($user_info);
            $display = 'view_success';
        }
    }
} else {
    $display = 'view_signup';
}

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));
