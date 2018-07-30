<?php
require dirname(__FILE__).'/session.php';
require dirname(__FILE__).'/inc.language.php';
require ROOT_PATH.'/../source/version.php';
$install_url = $_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']));
$base_url = (isset($_SERVER['HTTPS'])?'https':'http') . '://' . $install_url;

//Check database install situation
if (file_exists(dirname(dirname(dirname(__FILE__))).'/config/config_global.php')) {
    echo $lang_installed;
    echo '<br />';
    exit('<a href="../" style="color: blue;">Back</a>');
} else {
    echo '<h1>'.$lang_install_messageboard.'</h1>';
}
