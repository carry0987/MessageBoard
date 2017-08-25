<?php
$browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');

if($browser_lang == 'en' && empty($_COOKIE['language'])) {
    require dirname(__FILE__).'/../language/en_US.php';
} elseif($browser_lang == 'zh' && empty($_COOKIE['language'])) {
    require dirname(__FILE__).'/../language/zh_TW.php';
} elseif($_COOKIE['language'] == 'zh_TW') {
    require dirname(__FILE__).'/../language/zh_TW.php';
} elseif($_COOKIE['language'] == 'en_US') {
    require dirname(__FILE__).'/../language/en_US.php';
} else {
    require dirname(__FILE__).'/../language/en_US.php';
}

require dirname(__FILE__).'/../language/language.php';
?>