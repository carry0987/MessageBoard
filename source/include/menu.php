<?php
$menu_admin = '
        <li>
            <a href="'.$base_url.'/admin.php">'.$lang_admin.'</a>
        </li>
            ';

if(!empty($_SESSION['username'])) {
$menu_home = '
        <li>
            <a href="'.$base_url.'/home.php">'.$_SESSION['username'].'</a>
        </li>
            ';
}

$menu_login = '
        <li>
            <a href="'.$base_url.'/login.php">'.$lang_login.'</a>
        </li>
            ';
$menu_logout = '
        <li>
            <a href="'.$base_url.'/logout.php">'.$lang_logout.'</a>
        </li>
            ';
$menu_index = '
        <li>
            <a href="'.$base_url.'">'.$lang_index.'</a>
        </li>
            ';
$menu_signup = '
        <li>
            <a href="'.$base_url.'/signup.php">'.$lang_signup.'</a>
        </li>
            ';
$menu_setting = '
        <li>
            <a href="'.$base_url.'/setting.php">'.$lang_setting.'</a>
        </li>
            ';
?>
