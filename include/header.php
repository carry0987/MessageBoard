<?php
header('content-type:text/html;charset=utf-8');
require_once dirname(__FILE__).'/../admin/condb.php';
require dirname(__FILE__).'/../admin/input_safety.php';
require dirname(__FILE__).'/../admin/check_language.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <?php require dirname(__FILE__).'/meta_tag.php';?>
    <meta property="og:type" content="blog">
    <meta name="author" content="carry0987">
    <meta property="fb:admins" content="carry0987"/>
    <meta property="fb:app_id" content="1455782504488287">
    <meta property="og:image" content="https://www.nehscsa.com/icnc/fblogo1.png">
    <link href="./static/css.php?file=style.css" rel="stylesheet" type="text/css">
    <link href="./static/css.php?file=mobile-style.css" rel="stylesheet" type="text/css">
    <link href="./static/css.php?file=tablet-style.css" rel="stylesheet" type="text/css">
    <link href="./static/css.php?file=menu.css" rel="stylesheet" type="text/css">
    <link href="./static/css.php?file=mobile-menu.css" rel="stylesheet" type="text/css">
    <link href="./favicon.ico" rel="shortcut icon" />
    <script src="./static/jquery.php?file=jquery.min.js" type="text/javascript"></script>
    <noscript>
        <meta http-equiv="refresh" content="0;url=./static/noscript">
    </noscript>
</head>
<body>
    <div id="mainwrapper">
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
    echo '<h1>'.$lang_session_error.'</h1>';
    echo '<br />';
    echo '<h2>'.$lang_please.' <a href="./install" style="color: blue;">'.$lang_reinstall.'</a> '.$lang_messageboard.' !</h2>';
    $session_id = 'sessionerror';
}

session_name($session_id);
session_start();

require dirname(__FILE__).'/menu.php';

if (!empty($_SESSION['username'])) {
    echo "\t".'<div class="nowlogin">
        <span>'.$lang_now_login.'：'.$_SESSION['username'].'</span>
        </div>
        ';
} else {
    echo '<div class="nowlogin">
        <span>'.$lang_not_login.'</span>
        </div>
        ';
}

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
} else {
    $now_admin = 0;
}
?>
<header id="header">
    <div id="logo">
        <a href="./"><img id="logo-img" src="./static/icon/logo.png" alt="logo"></a>
    </div>
</header>