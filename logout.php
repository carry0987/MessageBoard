<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'data_update');
$load->loadFunction('filter', 'core');

//Template setting
$options = array(
    'template_dir' => 'template/common/',
    'css_dir' => 'static/css/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/',
    'auto_update' => true,
    'cache_lifetime' => 0,
    'cache_db' => $conn
);

$template = Template::getInstance();
$template->setOptions($options);

if (!empty($login['username']) && $login['username'] !== false) {
    $get_time = time();
    $update_user = DataUpdate::getInstance();
    $update_user->getConnection($conn);
    $update_user->updateLastlogin($get_time, $login['uid']);
    // Remove access token and state from session
    unset($_SESSION['github_access_token']);
    unset($_SESSION['state']);
    unset($_SESSION['userData']);
    unset($_SESSION['login_bid']);
    session_unset();
    session_destroy();
    $rememberMe = new RememberMe(SYSTEM_PATH);
    $rememberMe->getConnection($conn);
    $rememberMe->clearAuthCookie();
    $logout_permit = true;
    if (isset($_SERVER['HTTP_REFERER'])) {
        $url_from = checkRedirect($_SERVER['HTTP_REFERER']);
    } else {
        $url_from = false;
    }
} else {
    header('Location: ./');
}

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate('view_success.html'));
include($template->loadTemplate('footer_common.html'));
