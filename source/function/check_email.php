<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../../config/config_global.php';
require dirname(__FILE__).'/input_safety.php';
$browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
if($browser_lang == 'en' && empty($_COOKIE['language'])) {
    require dirname(__FILE__).'/../../language/en_US.php';
} elseif($browser_lang == 'zh' && empty($_COOKIE['language'])) {
    require dirname(__FILE__).'/../../language/zh_TW.php';
} elseif($_COOKIE['language'] == 'zh_TW') {
    require dirname(__FILE__).'/../../language/zh_TW.php';
} elseif($_COOKIE['language'] == 'en_US') {
    require dirname(__FILE__).'/../../language/en_US.php';
} else {
    require dirname(__FILE__).'/../../language/en_US.php';
}

require dirname(__FILE__).'/../../language/language.php';

$get_email = input_safety($_GET['email']);
$sql = 'SELECT email FROM user WHERE email = '."\"$get_email\"";
$result = $con->query($sql);

$regex = "/([\w\-]+\@[\w\-]+\.[\w\-]+)/";
function check_regex($strings, $standard) {
   if(preg_match($standard, $strings)) {
      return 1;
   } else {
      return 0;
   }
}

if(check_regex($get_email, $regex) == 1) {
if($result->num_rows > 0) {
    echo '<span style="color: red;">'.$lang_duplicate_email.'</span>';
} else {
    echo '<span style="color: green;">'.$lang_email_pass.'</span>';
}
} else {
    echo '<span style="color: red;">'.$lang_email_format_error.'</span>';
}
?>