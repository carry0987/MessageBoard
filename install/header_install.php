<?php
header('content-type:text/html;charset=utf-8');
require_once dirname(__FILE__).'/../admin/condb.php';
require dirname(__FILE__).'/../admin/input_safety.php';
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

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $lang_installing; ?></title>
    <link href="../static/css.php?file=style.css" rel="stylesheet" type="text/css">
    <link href="../static/css.php?file=mobile-style.css" rel="stylesheet" type="text/css">
    <link href="../static/css.php?file=tablet-style.css" rel="stylesheet" type="text/css">
    <link href="../static/css.php?file=menu.css" rel="stylesheet" type="text/css">
    <link href="../static/css.php?file=mobile-menu.css" rel="stylesheet" type="text/css">
    <link href="../static/css.php?file=command.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="../favicon.ico" />
    <noscript>
        <meta http-equiv="refresh" content="0;url=../static/noscript">
    </noscript>
</head>

<body>
    <div id="mainwrapper">
<header id="header">
    <div id="logo">
        <img id="logo-img" src="../static/icon/logo.png" alt="logo">
    </div>
</header>