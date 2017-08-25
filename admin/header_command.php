<?php
header('content-type:text/html;charset=utf-8');
require_once dirname(__FILE__).'/condb.php';
require dirname(__FILE__).'/input_safety.php';
$browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Waiting...</title>
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
<?php
$check_session_id_exists = 'SELECT session_id FROM config WHERE session_id IS NOT NULL';
$if_session_id_exists = $con->query($check_session_id_exists);

if($if_session_id_exists) {
if($if_session_id_exists->num_rows > 0) {
    $row = $if_session_id_exists->fetch_assoc();
    $session_id = trim($row['session_id']);
    $session_id = preg_replace('/\s(?=)/', '', $session_id);
}
} else {
    echo '<h1>Session Error !</h1>';
    echo '<br />';
    echo '<h2>'.$lang_please.' <a href="../install" style="color: blue;">'.$lang_reinstall.'</a> '.$lang_messageboard.' !</h2>';
    $session_id = 'sessionerror';
}

session_name($session_id);
session_start();

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

echo "\t".'<div class="nowlogin">
    <span>Waiting...</span>
    </div>
    ';

if (!empty($_SESSION['username'])) {
$now_login = $_SESSION['username'];
$is_admin = 'SELECT is_admin FROM user WHERE username = '."\"$now_login\"";
$is_admin_result = $con->query($is_admin);
if($is_admin_result) {
if($is_admin_result->num_rows > 0) {
    $admin = $is_admin_result->fetch_array();
    $now_admin = $admin['is_admin'];
}
}
}
?>
    <div id="logo">
        <img id="logo-img" src="../static/icon/logo.png" alt="logo">
    </div>
</header>