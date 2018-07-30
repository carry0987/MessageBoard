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

if (!empty($login['username'])) {
    $get_time = date('Y-m-d H:i:s');
    $update_user = new DataUpdate($conn);
    $update_user->updateLastlogin($get_time, $login['uid']);
    session_unset();
    session_destroy();
}

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate('view_logout.html'));
include($template->loadTemplate('footer_common.html'));
