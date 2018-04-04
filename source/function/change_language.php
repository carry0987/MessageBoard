<?php
/* Set Cookie Path */
$cookie_path = dirname(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path1 = dirname(dirname($cookie_path)).'/';

$select_language = trim($_GET['lang']);
$select_language = preg_replace('/\s(?=)/', '', $select_language);
if($select_language == 'zh_TW') {
  setcookie("language","zh_TW",time()+86400,$path1,null,true,true);
    echo '<script>';
    echo 'alert("Language : Chinese");history.back(0);';
    echo '</script>';
} else {
  $select_language == 'en_US';
  setcookie("language","en_US",time()+86400,$path1,null,true,true);
    echo '<script>';
    echo 'alert("Language : English");history.back(0);';
    echo '</script>';
}
