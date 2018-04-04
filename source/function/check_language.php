<?php
if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
} else {
    $browser_lang = 'en';
}

if($browser_lang == 'en' && empty($_COOKIE['language'])) {
    require ROOT_PATH.'/../language/en_US.php';
} elseif($browser_lang == 'zh' && empty($_COOKIE['language'])) {
    require ROOT_PATH.'/../language/zh_TW.php';
} elseif(isset($_COOKIE['language']) && $_COOKIE['language'] == 'zh_TW') {
    require ROOT_PATH.'/../language/zh_TW.php';
} elseif(isset($_COOKIE['language']) && $_COOKIE['language'] == 'en_US') {
    require ROOT_PATH.'/../language/en_US.php';
} else {
    require ROOT_PATH.'/../language/en_US.php';
}

require ROOT_PATH.'/../language/language.php';
