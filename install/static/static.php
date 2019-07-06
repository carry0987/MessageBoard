<?php
define('IN_INSTALL', true);
require dirname(__FILE__).'/../include/install_function.php';
$file = pathinfo(input_filter($_GET['file']), PATHINFO_EXTENSION);

switch ($file) {
    case ($file === 'css'):
        header('content-type:text/css;charset=utf-8');
        readfile('./css/'.input_filter($_GET['file']));
        break;
    default:
        echo 'Access Denied !';
        break;
}
