<?php
define('IN_INSTALL', true);
require dirname(__FILE__).'/install_function.php';
require dirname(__FILE__).'/inc.language.php';
require ROOT_PATH.'/../source/version.php';
$install_url = $_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']));
$base_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$install_url;
$web_lang_list = array(
    'en_US' => 'English',
    'zh_TW' => '繁體中文',
    'ja_JP' => '日本語',
    'th_TH' => 'Thai'
);

//Check database install situation
if (file_exists(dirname(dirname(dirname(__FILE__))).'/config/config_global.php')) {
    echo $LANG['install']['installed'];
    echo '<br />';
    exit('<a href="../" style="color: blue;">Back</a>');
} else {
    echo '<h1>'.$LANG['install']['install_messageboard'].'</h1>';
}
