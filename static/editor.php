<?php
require dirname(__FILE__). '/../source/function/input_safety.php';

$editor = pathinfo(input_safety($_GET['editor']), PATHINFO_EXTENSION);

switch ($editor) {
    case ($editor === 'js'):
        header('content-type:text/javascript;charset=utf-8');
        readfile('./js/editor/development/'.input_safety($_GET['editor']));
        break;
    case ($editor === 'css'):
        header('content-type:text/css;charset=utf-8');
        readfile('./js/editor/development/themes/'.input_safety($_GET['editor']));
        break;
    default:
        echo 'Access Denied !';
        break;
}
