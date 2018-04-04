<?php
require dirname(__FILE__).'/../../config/config_global.php';
function getrand_id() {
    $id_len = 16;
    $id = '';
    $word = 'abcdefghijkmnpqrstuvwxyz23456789';
    $len = strlen($word);
 
    for($i = 0; $i < $id_len; $i++) {
        $id .= $word[rand() % $len];
    }
    return $id;
}
 
$a = array();
 
for($x = 0; $x < 1; $x++) {
    $b = getrand_id();
    if(!in_array($b,$a)) {
        array_push($a,$b);
    } else {
    $x-=1;
    }
}

$check_session_id_exists = 'SELECT session_id FROM config';
$if_session_id_exists = $con->query($check_session_id_exists);

if($if_session_id_exists && $if_session_id_exists->num_rows > 0) {
    echo '';
} else {
    for($x=0; $x < 1; $x++) {
        $session_id = $a[$x];
    }
}
