<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/static/recaptcha/autoload.php';
ob_start();
require dirname(__FILE__).'/include/header.php';
require dirname(__FILE__).'/function/check_database.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Signup - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

/* reCaptcha */
$recaptcha_sql = 'SELECT recaptcha_site,recaptcha_secret FROM config';
$recaptcha_result = $con->query($recaptcha_sql);
if($recaptcha_result) {
  $recaptcha_row = $recaptcha_result->fetch_assoc();
} else {
  header('Location: ./');
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
?>

<?php
if(empty($_SESSION['username'])) {
    echo '
    <div id="cssmenu">
        <ul>
    ';
    echo $menu_index;
    echo $menu_login;
    echo '
        </ul>
    </div>
    ';

    echo '
    <script src="https://www.google.com/recaptcha/api.js?hl='.$recaptcha_lang.'&onload=onloadCallback&render=explicit"  type="text/javascript" async defer></script>
    <script type="text/javascript">
    var onloadCallback = function() {
        grecaptcha.render("html_element", {
            "sitekey": "'.$siteKey.'",
            "theme": "white"
        });
    };
    </script>
    <div class="signup_div">
        <form id="signup_form" class="signup_form" name="signup" action="./function/user_add.php" method="post" onsubmit="return signup_check()">
            <table class="alert">
                <tr>
                    <td>
                        <span id="checkbox"></span>
                    </td>
                </tr>
            </table>
            <table class="signup">
                <tr>
                    <td class="signup-a">'.$lang_username.'：</td>
                    <td class="signup-a">
                        <input class="signup-input" type="text" name="username" id="username" placeholder="Username" maxlength="20" oninput="check_username();" onpropertychange="check_username();">
                    </td>
                </tr>
                <tr>
                    <td class="signup-a">'.$lang_password.'：</td>
                    <td class="signup-a">
                        <input class="signup-input" type="password" name="password" id="password" placeholder="Password" maxlength="20">
                    </td>
                </tr>
                <tr>
                    <td class="signup-a">'.$lang_confirm.'：</td>
                    <td class="signup-a">
                        <input class="signup-input" type="password" name="pdr" id="pdr" placeholder="Confirm Password" maxlength="20">
                    </td>
                </tr>
                <tr>
                    <td class="signup-a">'.$lang_email.'：</td>
                    <td class="signup-a">
                        <input class="signup-input" type="text" name="email" id="email" placeholder="example@example.com" maxlength="30" oninput="check_email();" onpropertychange="check_email();">
                    </td>
                </tr>
            </table>
            <br />
            <div id="html_element"></div>
            <div class="submit">
                <button type="submit" name="submit">'.$lang_signup.'</button>
            </div>
        </form>
    </div>
    ';
} else {
    echo '<meta http-equiv="refresh" content="0;url=./" />';
    exit();
}
?>

<script>
function signup_check() {
    var username = document.signup.username.value;
    var password1 = document.signup.password.value;
    var password2 = document.signup.pdr.value;
    var email_input = document.signup.email.value;
    var regex = new RegExp("^[A-Za-z0-9]+$");
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(username == '')
    {
      <?php echo "alert(\"$lang_username_empty\");"; ?>
      return false;
    }
    else if(username.length < 6)
    {
      <?php echo "alert(\"$lang_username_length\");"; ?>
      return false;
    }
    else if(!regex.test(username))
    {
      <?php echo "alert(\"$lang_username_rule\");"; ?>
      return false;
    }
    else if(password1 == '' || password2 == '')
    {
      <?php echo "alert(\"$lang_password_empty\");"; ?>
      return false;
    }
    else if(password1 != password2)
    {
      <?php echo "alert(\"$lang_repassword_error\");"; ?>
      return false;
    }
    else if(password1.length < 8 || password2.length < 8 || !regex.test(password1) || !regex.test(password2))
    {
      <?php echo "alert(\"$lang_password_rule\");"; ?>
      document.getElementById('password').value = "";
      document.getElementById('pdr').value = "";
      return false;
    }
    else if(email == '')
    {
      <?php echo "alert(\"$lang_email_empty\");"; ?>
      return false;
    }
    else if(!re.test(email_input))
    {
      <?php echo "alert(\"$lang_email_format_error\");"; ?>
      return false;
    }
return true;
}

var XHR;
function createXHR() {
    if (window.ActiveXObject) {
        XHR = new ActiveXObject('Microsoft.XMLHTTP');
    } else if (window.XMLHttpRequest) {
        XHR = new XMLHttpRequest();
    }
}

function check_username() {
    var username = document.signup.username.value;
    createXHR();
    XHR.open("GET", "./function/check_username.php?username=" + username, true);
    XHR.onreadystatechange = response;
    XHR.send(null);
}

function check_email() {
    var email = document.signup.email.value;
    createXHR();
    XHR.open("GET", "./function/check_email.php?email=" + email, true);
    XHR.onreadystatechange = response;
    XHR.send(null);
}

function response() {
    if (XHR.readyState == 4) {
        if (XHR.status == 200) {
            var textHTML = XHR.responseText;
            document.getElementById('checkbox').innerHTML = textHTML;
        }
    }
}
</script>
<?php require dirname(__FILE__).'/include/footer.php'; ?>