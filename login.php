<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'data_update');
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

//Check login
if (!empty($login['username'])) {
    header('Location: '.$base_url.'');
}

$account_error = $password_error = '';
if (isset($_POST['submit'])) {
    $login_permit = true;
    $account = $_POST['account'];
    $get_password = input_filter($_POST['password']);
    $login_query = 'SELECT id,username,password FROM user WHERE username = ? OR email = ?';
    $login_stmt = $conn->stmt_init();

    if ($login_stmt->prepare($login_query)) {
        $login_stmt->bind_param('ss', $account, $account);
        $login_stmt->execute();
        $login_stmt->bind_result($id, $username, $password);
        $login_result = $login_stmt->get_result();
        while ($login_row = $login_result->fetch_assoc()) {
            $login_id = $login_row['id'];
            $login_username = input_filter($login_row['username']);
            $check_password = $login_row['password'];
        }
    } else {
        header('Location: '.$base_url);
        exit();
    }

    if ($login_result->num_rows == 0) {
        $account_error = $lang_account_not_exist;
        $login_permit = false;
    } elseif (!password_verify($get_password, $check_password)) {
        $password_error = $lang_wrong_password;
        $login_permit = false;
    }

    if ($login_result->num_rows != 0 && $login_permit === true) {
        $_SESSION['username'] = $login_username;
        $get_time = date('Y-m-d H:i:s');
        $update_user = new DataUpdate($conn);
        $update_user->updateLastlogin($get_time, $login_id);
        $display = 'view_success';
    } else {
        $display = 'view_login';
    }
} else {
    $display = 'view_login';
}

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));
