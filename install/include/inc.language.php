<?php
define('ROOT_PATH', dirname(__FILE__).'/../');
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
} else {
    $browser_lang = 'en';
}

if (!isset($_COOKIE['language'])) {
    switch ($browser_lang) {
        case ($browser_lang == 'en'):
            require ROOT_PATH.'/../language/en_US.php';
            break;
        case ($browser_lang == 'zh'):
            require ROOT_PATH.'/../language/zh_TW.php';
            break;
        case ($browser_lang == 'ja'):
            require ROOT_PATH.'/../language/ja_JP.php';
            break;
        case ($browser_lang == 'th'):
            require ROOT_PATH.'/../language/th_TH.php';
            break;
        default:
            require ROOT_PATH.'/../language/en_US.php';
            break;
    }
} elseif (isset($_COOKIE['language'])) {
    switch ($_COOKIE['language']) {
        case ($_COOKIE['language'] == 'en_US'):
            require ROOT_PATH.'/../language/en_US.php';
            break;
        case ($_COOKIE['language'] == 'zh_TW'):
            require ROOT_PATH.'/../language/zh_TW.php';
            break;
        case ($_COOKIE['language'] == 'ja_JP'):
            require ROOT_PATH.'/../language/ja_JP.php';
            break;
        case ($_COOKIE['language'] == 'th_TH'):
            require ROOT_PATH.'/../language/th_TH.php';
            break;
        default:
            require ROOT_PATH.'/../language/en_US.php';
            break;
    }
}
require ROOT_PATH.'/../language/language.php';
