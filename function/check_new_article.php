<?php
header('content-type:text/html;charset=utf-8');
require_once dirname(__FILE__).'/condb.php';
require_once dirname(__FILE__). '/input_safety.php';
$today_string = date("Y-m-d");
$today_sql = 'SELECT id FROM article WHERE DATE(date) = '."\"$today_string\"";
$today_result = $con->query($today_sql);
$today = $today_result->num_rows;

echo input_safety($today);
?>