<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../admin/session.php';
require dirname(__FILE__).'/header_install.php';
?>

<?php
$check_table_msg_exists = 'SELECT id FROM msg';
$check_table_user_exists = 'SELECT id FROM user';
$check_table_config_exists = 'SELECT id FROM config';
$check_session_id_exists = 'SELECT session_id FROM config WHERE session_id IS NOT NULL';
$if_msg_exists = $con->query($check_table_msg_exists);
$if_user_exists = $con->query($check_table_user_exists);
$if_config_exists = $con->query($check_table_config_exists);
$if_session_id_exists = $con->query($check_session_id_exists);

if($if_msg_exists && $if_user_exists && $if_config_exists && $if_session_id_exists) {
if($if_msg_exists->num_rows > 0 && $if_user_exists->num_rows > 0 && $if_config_exists->num_rows > 0 && $if_session_id_exists->num_rows > 0) {
    echo '<script>';
    echo 'alert("'.$lang_installed.'");location.href="../";';
    echo '</script>';
}
} else {
    echo '<h1>'.$lang_install_messageboard.'</h1>

<div class="install">
    <form action="./data.php" method="post" name="install" onsubmit="return check()">
        <span id="checkbox"></span>
        <br />
        <span id="display"></span>
        <table>
            <tbody>
                <tr>
                    <td><label>'.$lang_web_name.'</label></td>
                    <td><input type="text" name="web_name" placeholder="Example" onblur="web();"></td>
                </tr>
                <tr>
                    <td><label>'.$lang_web_email.'</label></td>
                    <td><input type="text" name="web_email" placeholder="example@email.com" onblur="email();"></td>
                </tr>
                <tr>
                    <td><label>'.$lang_username.'</label></td>
                    <td><input type="text" name="admin_username" placeholder="Admin" onblur="username();"></td>
                </tr>
                <tr>
                    <td><label>'.$lang_password.'</label></td>
                    <td><input type="password" maxlength="20" name="admin_password" placeholder="Password" oninput="check();" onpropertychange="check()"></td>
                </tr>
                <tr>
                    <td><label>'.$lang_confirm.'</label></td>
                    <td><input type="password" maxlength="20" name="admin_confirm_password" placeholder="Confirm Password" oninput="check();" onpropertychange="check()"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit" class="submit">'.$lang_install_next.'</button>
    </form>
</div>';
}
?>

<script>
function check() {
    var password1 = document.install.admin_password.value;
    var password2 = document.install.admin_confirm_password.value;
    var password_max = document.install.admin_password.value.length;

    if (password_max >= 20) {
        document.getElementById('display').innerHTML = "<span style='color: red'>Word Count : 20</span>";
    } else {
        document.getElementById('display').innerHTML = "<span style='color: green'>Word Count : </span>"+password_max;
    }

    if (password1 != password2) {
        document.getElementById('checkbox').innerHTML = '<span style="color: red"><?php echo $lang_repassword_error; ?></span>';
        return false;
    } else if (password1 == '') {
        document.getElementById('checkbox').innerHTML = '<span style="color: red"><?php echo $lang_password_empty; ?></span>';
        return false;
    } else if (password_max > 20) {
        return false;
    } else {
        document.getElementById('checkbox').innerHTML = '<span style="color: green"><?php echo $lang_repassword_pass; ?></span>';
        return true;
    }
}

function web() {
    var web = document.install.web_name.value;
    if (web == '') {
        document.getElementById('checkbox').innerHTML = '<span style="color: red"><?php echo $lang_web_name_empty; ?></span>';
        return false;
    } else {
        document.getElementById('checkbox').innerHTML = '<span style="color: green"><?php echo $lang_web_name_pass; ?></span>';
        return true;
    }
}

function email() {
    var email = document.install.web_email.value;
    if (email == '') {
        document.getElementById('checkbox').innerHTML = '<span style="color: red"><?php echo $lang_web_email_empty; ?></span>';
        return false;
    } else {
        document.getElementById('checkbox').innerHTML = '<span style="color: green"><?php echo $lang_web_email_pass; ?></span>';
        return true;
    }
}

function username() {
    var username = document.install.admin_username.value;
    if (name == '') {
        document.getElementById('checkbox').innerHTML = '<span style="color: red"><?php echo $lang_username_empty; ?></span>';
        return false;
    } else {
        document.getElementById('checkbox').innerHTML = '<span style="color: green"><?php echo $lang_username_pass; ?></span>';
        return true;
    }
}
</script>

<?php require dirname(__FILE__).'/footer_install.php'; ?>
