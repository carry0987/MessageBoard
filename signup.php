<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/include/header.php';
require dirname(__FILE__).'/admin/check_database.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Signup - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

//Check Login
if (!empty($_SESSION['username']))
    echo '<meta http-equiv="refresh" content="0;url=./" />';

    echo '
    <div id="cssmenu">
        <ul>
    ';
    echo $menu_index;
    echo $menu_login;
    echo $menu_message;
    echo '
        </ul>
    </div>
    ';

    $OK = 1;
    $UsernameErr = $PasswordErr = '';
    if(isset($_POST['submit'])){
        $Username = input_safety($_POST['username']);
        $Password = input_safety($_POST['password']);
        $_Password = input_safety($_POST['pdr']);
        if(empty($Username)){
            $UsernameErr = $lang_username_empty;
            $OK=0;
        }
        elseif (!preg_match("/^[a-zA-Z0-9]+$/",$Username)){
            $UsernameErr = $lang_username_rule;
            $OK = 0;
        }
        if (empty($Password)){
            $PasswordErr = $lang_password_empty;
            $OK = 0;
        }
        if ($_Password != $Password){
            $PasswordErr = $lang_repassword_error;
            $OK = 0;
        }
        if ($OK){
            $Permit = 1;
            $sql = 'SELECT username FROM user WHERE username = '."\"$Username\"";
            $result = $con->query($sql);
            $row = $result->fetch_assoc();
            if($result->num_rows > 0){
                $UsernameErr = $lang_duplicate_username;
                $Permit = 0;
            }
            if ($Permit){
                $sql = 'INSERT INTO user (username, password, is_admin) VALUES ('."\"$Username\"".','."\"$Password\"".','.'0'.')';
                $con->query($sql);
            echo '
            <script type="text/javascript">
            setTimeout("countdown()", 1000);
            function countdown() {
                var s = document.getElementById("refresh");
                s.innerHTML = s.innerHTML - 1;
                if (s.innerHTML == 0) {
                    window.location = "login.php";
                } else {
                    document.getElementById("username").readOnly = true;
                    document.getElementById("password").readOnly = true;
                    document.getElementById("pdr").readOnly = true;
                    setTimeout("countdown()", 1000);
                }
            }
            </script>
            ';
            echo '<div class="infomation">';
            echo '<a>'.$lang_signup_info_1.'<span id="refresh">1</span>'.$lang_signup_info_2.'</a>';
            echo '<br />';
            echo '<a class="ifnorefresh" href="login.php">'.$lang_no_refresh.'</a>';
            echo '</div>';
            }
        }
    }
?>
<?php
if(!isset($_POST['submit'])) {
    echo '
<div class="signup_div">
    <form class="signup_form" name="signup" action="signup.php" method="post" onsubmit="return signup_check()">
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
        </table>
        <br />
        <div class="submit">
            <button type="submit" name="submit">Sighup</button>
        </div>
    </form>
</div>';
}
?>
<script>
function signup_check() {
    var username = document.signup.username.value;
    var password1 = document.signup.password.value;
    var password2 = document.signup.pdr.value;
    var regex = new RegExp("^[A-Za-z0-9]+$");
    if(username == '')
    {
      <?php echo "alert(\"$lang_username_empty\");"; ?>
      document.getElementById("username").focus();
      return false;
    }
    else if(username.length < 6)
    {
      <?php echo "alert(\"$lang_username_length\");"; ?>
      document.getElementById("username").focus();
      return false;
    }
    else if(!regex.test(username))
    {
      <?php echo "alert(\"$lang_username_rule\");"; ?>
      document.getElementById("username").focus();
      return false;
    }
    else if(password1 == '' || password2 == '')
    {
      <?php echo "alert(\"$lang_password_empty\");"; ?>
      document.getElementById("password").focus();
      return false;
    }
    else if(password1 != password2)
    {
      <?php echo "alert(\"$lang_repassword_error\");"; ?>
      document.getElementById("pdr").focus();
      return false;
    }
    else if(password1.length < 8 || password2.length < 8 || !regex.test(password1) || !regex.test(password2))
    {
      <?php echo "alert(\"$lang_password_rule\");"; ?>
      document.getElementById('password').value = "";
      document.getElementById('pdr').value = "";
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
    XHR.open("GET", "./admin/check_username.php?username=" + username, true);
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
<?php
if($UsernameErr != '')
{
echo "alert(\"$UsernameErr\");location.href='./signup.php';\n";
}

if($PasswordErr != '')
{
echo "alert(\"$PasswordErr\");location.href='./signup.php';\n";
}
?>
</script>
<?php require dirname(__FILE__).'/include/footer.php'; ?>