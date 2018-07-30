<?php
/* Avoid Attack */
function input_filter($value) {
    $value = str_replace("'", "\"", "$value");
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}
