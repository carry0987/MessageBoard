<?php
header('content-type:text/html;charset=utf-8');
require_once dirname(__FILE__).'/condb.php';
$check_table_msg_exists = 'SELECT id FROM msg';
$check_table_user_exists = 'SELECT id FROM user';
$check_table_config_exists = 'SELECT id FROM config';
$if_msg_exists = $con->query($check_table_msg_exists);
$if_user_exists = $con->query($check_table_user_exists);
$if_config_exists = $con->query($check_table_config_exists);

if($if_msg_exists->num_rows > 0) {
    echo '';
} elseif($if_user_exists->num_rows > 0) {
    echo '';
} elseif($if_config_exists->num_rows > 0) {
    echo '';
} else {
    header('Location: ./install');
}
?>