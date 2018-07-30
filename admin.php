<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'page', 'data_create', 'data_update', 'data_delete', 'admin');
$load->loadFunction('filter');

//Template setting
$options = array(
    'template_dir' => 'template/admin/',
    'css_dir' => 'static/css/admin/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/admin/',
    'auto_update' => true,
    'cache_lifetime' => 0,
);

$template = Template::getInstance();
$template->setOptions($options);

//Breadcrumb
$admin_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Check mod
$class['index'] = '';
$class['user'] = '';
$class['article'] = '';
$class['board'] = '';
$class['category'] = '';
$class['recaptcha'] = '';

if (!empty($login['username']) && $login['admin'] === true) {
    $show_admin = true;
    if (isset($_GET['mod'])) {
        switch ($_GET['mod']) {
            case ($_GET['mod'] === 'index'):
                $class['index'] = 'active';
                $display = 'view_admin';
                $admin_config = new Admin($conn);
                $admin = $admin_config->showConfig();
                break;
            case ($_GET['mod'] === 'user'):
                $class['user'] = 'active';
                $display = 'manager_user';
                break;
            case ($_GET['mod'] === 'article'):
                $class['article'] = 'active';
                $display = 'manager_article';
                break;
            case ($_GET['mod'] === 'board'):
                $class['board'] = 'active';
                $display = 'manager_board';
                break;
            case ($_GET['mod'] === 'category'):
                $class['category'] = 'active';
                $display = 'manager_category';
                break;
            case ($_GET['mod'] === 'recaptcha'):
                $class['recaptcha'] = 'active';
                $display = 'manager_recaptcha';
                break;
            default:
                $class['index'] = 'active';
                $display = 'view_admin';
                break;
        }
    } else {
        $class['index'] = 'active';
        $display = 'view_admin';
        $admin_config = new Admin($conn);
        $admin = $admin_config->showConfig();
    }
} else {
    $show_admin = false;
    $display = 'view_denied';
}

//Check change setting
if (isset($_GET['update'])) {
    switch ($_GET['update']) {
        case ($_GET['update'] === 'setting'):
            if (isset($_POST['web_name']) && isset($_POST['web_description'])) {
                $update['config'] = new DataUpdate($conn);
                $update['config']->updateConfig($_POST['web_name'], $_POST['web_description']);
                sleep(1);
                $display = 'view_success';
            }
            break;
        default:
            break;
    }
}

include($template->loadTemplate('header_admin.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_admin.html'));
