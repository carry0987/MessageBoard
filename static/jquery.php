<?php
header('content-type:text/javascript;charset=utf-8');
$dir = "./js/jquery/2.2.4/" . $_GET['file'];
readfile($dir);
?>