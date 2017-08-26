<?php
$menu_admin = '
        <li>
            <a href="admin.php">'.$lang_admin.'</a>
        </li>
            ';

if(!empty($_SESSION['username'])) {
$menu_homepage = '
        <li>
            <a href="homepage.php">'.$_SESSION['username'].'</a>
        </li>
            ';
}

$menu_login = '
        <li>
            <a href="login.php">'.$lang_login.'</a>
        </li>
            ';
$menu_logout = '
        <li>
            <a href="logout.php">'.$lang_logout.'</a>
        </li>
            ';
$menu_index = '
        <li>
            <a href="./">'.$lang_index.'</a>
        </li>
            ';
$menu_signup = '
        <li>
            <a href="signup.php">'.$lang_signup.'</a>
        </li>
            ';
$menu_setting = '
        <li>
            <a href="./setting.php">'.$lang_setting.'</a>
        </li>
            ';
?>
