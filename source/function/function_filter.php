<?php
/* Avoid Attack */
function input_filter($value)
{
    $value = str_replace("'", "\"", $value);
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}

function array_sanitize($array)
{
    foreach ($array as $key => $value) {
        $array[$key] = str_replace("'", "\"", $array[$key]);
        $array[$key] = trim($array[$key]);
        $array[$key] = stripslashes($array[$key]);
        $array[$key] = htmlspecialchars($array[$key]);
    }
    return $array;
}

function stripValue($value, $count_limit)
{
    if (mb_strlen($value,'utf-8') > $count_limit) {
        $value = mb_substr($value,0,$count_limit,'utf-8').'...';
    } else {
        $value = mb_substr($value,0,$count_limit,'utf-8');
    }
    return $value;
}
