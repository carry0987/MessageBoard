<?php
define('ROOT_PATH', dirname(__FILE__).'/../');
require ROOT_PATH.'/../source/class/class_language.php';
$lang_path = dirname(dirname($_SERVER['PHP_SELF']));
// Check language
$load_language = new Language($lang_path);
$load_language->setLanguageFile(array('admin', 'common', 'database', 'install'));
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !isset($_COOKIE['language'])) {
    $browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
    $lang_file = $load_language->loadLanguage($browser_lang);
    foreach ($lang_file as $lang) {
        require ROOT_PATH.'/..'.$lang;
    }
    $current_lang = $load_language->getCurrentLang();
} elseif (isset($_COOKIE['language'])) {
    $lang_file = $load_language->loadLanguage($_COOKIE['language']);
    foreach ($lang_file as $lang) {
        require ROOT_PATH.'/..'.$lang;
    }
    $current_lang = $load_language->getCurrentLang();
} else {
    $browser_lang = 'en';
    $lang_file = $load_language->loadLanguage($browser_lang);
    foreach ($lang_file as $lang) {
        require ROOT_PATH.'/..'.$lang;
    }
    $current_lang = $load_language->getCurrentLang();
}

// Change language
if (isset($_POST['lang'])) {
    $load_language->setLanguage($_POST['lang'], (isset($_SERVER['HTTPS'])?true:false));
} elseif (isset($_GET['lang'])) {
    $load_language->setLanguage($_GET['lang'], (isset($_SERVER['HTTPS'])?true:false));
}
