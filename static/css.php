<?php
header('content-type:text/css;charset=utf-8');
$dir = "./css/" . $_GET['file'];
readfile($dir);
?>