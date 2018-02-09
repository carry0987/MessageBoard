<?php
header('content-type:text/javascript;charset=utf-8');
$dir = "./js/jquery/3.3.1/" . $_GET['file'];
readfile($dir);
?>