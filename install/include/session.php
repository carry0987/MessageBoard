<?php
function getrand_id() {
    $id_len = 16;
    $id = '';
    $word = 'abcdefghijkmnpqrstuvwxyz23456789';
    $len = strlen($word);
 
    for($i = 0; $i < $id_len; $i++) {
        $id = $id.$word[rand() % $len];
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

for($x=0; $x < 1; $x++) {
    $session_id = $a[$x];
}
