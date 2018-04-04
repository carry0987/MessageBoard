<?php
header('content-type:text/html;charset=utf-8');
require dirname(dirname(__FILE__)).'/static/recaptcha/autoload.php';
require dirname(__FILE__).'/../include/header.php';
require dirname(__FILE__).'/check_database.php';

/* reCaptcha */
$recaptcha_sql = 'SELECT recaptcha_site,recaptcha_secret FROM config WHERE id = 1';
$recaptcha_result = $con->query($recaptcha_sql);
if($recaptcha_result) {
  $recaptcha_row = $recaptcha_result->fetch_assoc();
} else {
  header('Location: ../');
}
$siteKey = $recaptcha_row['recaptcha_site'];
$secret = $recaptcha_row['recaptcha_secret'];
$resp = '';
if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
} else {
    $browser_lang = 'en';
}
if($browser_lang == 'en' && empty($_COOKIE['language'])) {
    $recaptcha_lang = 'en_US';
} elseif($browser_lang == 'zh' && empty($_COOKIE['language'])) {
    $recaptcha_lang = 'zh_TW';
} elseif(isset($_COOKIE['language']) && $_COOKIE['language'] == 'zh_TW') {
    $recaptcha_lang = 'zh_TW';
} elseif(isset($_COOKIE['language']) && $_COOKIE['language'] == 'en_US') {
    $recaptcha_lang = 'en_US';
} else {
    $recaptcha_lang = 'en_US';
}

//Check Login
if(empty($_SESSION['username'])) {
    $OK = 1;
    $UsernameErr = $PasswordErr = $EmailErr = $reCaptchaErr = '';
    if(isset($_POST['submit']) && isset($_POST['g-recaptcha-response'])) {
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
        if($resp->isSuccess() == true) {
            echo '';
        } else {
            echo "
            <script>
                alert(\"$lang_recaptcha_error\");location.href='../signup.php';\n;
            </script>
            ";
            exit();
        }
        $Username = input_safety($_POST['username']);
        $Password = input_safety($_POST['password']);
        $_Password = input_safety($_POST['pdr']);
        $Email = input_safety($_POST['email']);
        $get_time = date('Y-m-d H:i:s');

        if(empty($Username)) {
            $UsernameErr = $lang_username_empty;
            $OK = 0;
        }
        elseif(!preg_match("/^[a-zA-Z0-9]+$/",$Username)) {
            $UsernameErr = $lang_username_rule;
            $OK = 0;
        }
        if(empty($Password)) {
            $PasswordErr = $lang_password_empty;
            $OK = 0;
        }
        if($_Password != $Password) {
            $PasswordErr = $lang_repassword_error;
            $OK = 0;
        }
        if(empty($Email)) {
            $EmailErr = $lang_email_empty;
            $OK = 0;
        } elseif(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$Email)) {
            $EmailErr = $lang_email_format_error;
            $OK = 0;
        }
        if($OK) {
            $Permit = 1;
            $insert_password = password_hash($Password, PASSWORD_DEFAULT);
            $sql = 'SELECT username FROM user WHERE username = '."\"$Username\"";
            $result = $con->query($sql);
            $row = $result->fetch_assoc();
            $email_sql = 'SELECT email FROM user WHERE email = '."\"$Email\"";
            $email_result = $con->query($email_sql);
            $email_row = $email_result->fetch_assoc();
            if($result->num_rows > 0) {
                $UsernameErr = $lang_duplicate_username;
                $Permit = 0;
            }
            if($email_result->num_rows > 0) {
                $EmailErr = $lang_duplicate_email;
                $Permit = 0;
            }

            /* Email Authenticate */
            $email_sql = 'SELECT web_name FROM config WHERE id = 1';
            $email_result = $con->query($email_sql);
            if($email_result) {
              $email_row = $email_result->fetch_assoc();
              $main_name = $email_row['web_name'];
            } else {
              header('Location: ../');
            }
            $To = "$Email";
            $Subject = $lang_welcome_to.$main_name;
            $Msg = $lang_dear.' '.$Username."\n".$lang_welcome_to.' '.$main_name;
            if(!mail("$To", "$Subject", "$Msg")) {
                $Permit = 0;
                echo '
                    <script>
                        alert("Email System Error !");location.href="../signup.php";
                    </script>
                ';
                exit();
            }

            if($Permit) {
                $add_user_sql = 'INSERT INTO user (username, password, email, is_admin, date)
                 VALUES ('."\"$Username\"".','."\"$insert_password\"".','."\"$Email\"".','.'0'.','."\"$get_time\"".')';
                $con->query($add_user_sql);
            echo '
            <script type="text/javascript">
            setTimeout("countdown()", 1000);
            function countdown() {
                var s = document.getElementById("refresh");
                s.innerHTML = s.innerHTML - 1;
                if (s.innerHTML == 0) {
                    window.location = "../login.php";
                } else {
                    document.getElementById("username").readOnly = true;
                    document.getElementById("password").readOnly = true;
                    document.getElementById("pdr").readOnly = true;
                    document.getElementById("email").readOnly = true;
                    setTimeout("countdown()", 1000);
                }
            }
            </script>
            ';
            echo '<div class="infomation">';
            echo '<a>'.$lang_signup_info_1.'<span id="refresh">1</span>'.$lang_signup_info_2.'</a>';
            echo '<br />';
            echo '<a class="ifnorefresh" href="../login.php">'.$lang_no_refresh.'</a>';
            echo '</div>';
            }
        }
    } else {
        echo '<meta http-equiv="refresh" content="0;url=../" />';
        exit();
    }
    } else {
        echo '<meta http-equiv="refresh" content="0;url=../" />';
        exit();
    }
?>

<script>
<?php
if($UsernameErr != '') {
    echo "alert(\"$UsernameErr\");location.href='../signup.php';\n";
}

if($PasswordErr != '') {
    echo "alert(\"$PasswordErr\");location.href='../signup.php';\n";
}

if($EmailErr != '') {
    echo "alert(\"$EmailErr\");location.href='../signup.php';\n";
}
?>
</script>

<?php require dirname(__FILE__).'/../include/footer.php'; ?>