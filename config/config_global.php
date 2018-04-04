<?php
header('content-type:text/html;charset=utf-8');
error_reporting(E_ALL | E_STRICT);
define('DB_HOST', 'localhost');           //Your database host
define('DB_USER', 'root');                //Your database username
define('DB_PASSWORD', 'root');            //Your database password
define('DB_NAME', 'messageboard');        //Your database name
define('DB_PORT', '3306');                //Your database connect port
date_default_timezone_set('Asia/Taipei'); //Your Timezone
$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$check_connect = $con->query("SET CHARACTER SET utf8mb4");
if(!$con || !$check_connect){
    echo '<h1>Connect Error !</h1>';
    echo '<br />';
    echo '<h2>Error Info :'.mysqli_connect_error().'</h2>';
    die();
} else {
    echo '';
}
?>