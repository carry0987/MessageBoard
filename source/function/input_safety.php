<?php
/* Avoid Attack */
function input_safety($value) {
    $value = str_replace("'", "\"", "$value");
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}
