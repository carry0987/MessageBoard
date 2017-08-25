<?php
header('content-type:text/javascript;charset=utf-8');
$dir = "./js/" . $_GET['file'];
readfile($dir);
?>