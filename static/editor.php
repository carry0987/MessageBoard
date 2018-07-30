<?php
require dirname(__FILE__). '/../source/function/function_filter.php';

$editor = pathinfo(input_filter($_GET['editor']), PATHINFO_EXTENSION);

switch ($editor) {
    case ($editor === 'js'):
        header('content-type:text/javascript;charset=utf-8');
        readfile('./editor/'.input_filter($_GET['editor']));
        break;
    case ($editor === 'css'):
        header('content-type:text/css;charset=utf-8');
        readfile('./editor/themes/'.input_filter($_GET['editor']));
        break;
    default:
        echo 'Access Denied !';
        break;
}
