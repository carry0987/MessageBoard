<?php
header('content-type:text/html;charset=utf-8');
$options = array('http' => array('user_agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36'));
$context = stream_context_create($options);
$response = file_get_contents('https://api.github.com/repos/carry0987/Messageboard/releases/latest', false, $context);
$obj = json_decode($response);
echo $obj->tag_name;
?>