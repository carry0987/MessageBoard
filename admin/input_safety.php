<?php
/* Avoid Attack */
function input_safety($date){
    $date = str_replace("'","\"","$date" );
    $date = trim($date);
    $date = stripslashes($date);
    $date = htmlspecialchars($date);
    return $date;
}
?>