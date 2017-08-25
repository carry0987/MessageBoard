<?php
header('content-type:image/svg+xml;charset=utf-8');
$dir = "./icon/" . $_GET['file'];
readfile($dir);
?>