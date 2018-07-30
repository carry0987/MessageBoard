<?php
/* Avoid Attack */
function input_filter($value)
{
    $value = str_replace("'", "\"", "$value");
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}

function stripValue($value)
{
    if (mb_strlen($value,'utf-8') > 35) {
        $value = mb_substr($value,0,35,'utf-8').'...';
    } else {
        $value = mb_substr($value,0,35,'utf-8');
    }
    return $value;
}