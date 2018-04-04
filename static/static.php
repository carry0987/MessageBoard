<?php
require dirname(__FILE__). '/../source/function/input_safety.php';

$file = pathinfo(input_safety($_GET['file']), PATHINFO_EXTENSION);

switch ($file) {
    case ($file === 'js'):
        header('content-type:text/javascript;charset=utf-8');
        readfile('./js/'.input_safety($_GET['file']));
        break;
    case ($file === 'css'):
        header('content-type:text/css;charset=utf-8');
        readfile('./css/'.input_safety($_GET['file']));
        break;
    case ($file === 'svg'):
        header('content-type:image/svg+xml;charset=utf-8');
        readfile('./icon/'.input_safety($_GET['file']));
        break;
    default:
        echo 'Access Denied !';
        break;
}
