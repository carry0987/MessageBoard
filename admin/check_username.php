<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/condb.php';
require dirname(__FILE__).'/input_safety.php';
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

$getname = input_safety($_GET['username']);
$sql = 'SELECT username FROM user WHERE username = '."\"$getname\"";
$result = $con->query($sql);

$regex = "/^([0-9A-Za-z]+)$/";
function check_regex($strings, $standard) {
   if(preg_match($standard, $strings)) {
      return 1;
   } else {
      return 0;
   }
}

$len = mb_strlen($getname,'utf-8');

if($len >= 6) {
if(check_regex($getname, $regex) == 1) {
if($result->num_rows > 0) {
    echo '<span style="color: red;">'.$lang_duplicate_username.'</span>';
} else {
    echo '<span style="color: green;">'.$lang_username_pass.'</span>';
}
} else {
    echo '<span style="color: red;">'.$lang_username_rule.'</span>';
}
} else {
    echo '<span style="color: red;">'.$lang_username_length.'</span>';
}
?>