<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/class_language.php';
require dirname(__FILE__).'/class_metatag.php';

//Set ROOT_PATH
define('ROOT_PATH', dirname(__FILE__).'/../../');
$host['link'] = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'];

//Check config
if (!file_exists(ROOT_PATH.'/config/config_global.php')) {
    header('Location: ./install');
} else {
    require ROOT_PATH.'/config/config_global.php';
    require ROOT_PATH.'/source/version.php';
    $base_url = $host['link'].SYSTEM_PATH;
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        $conn->query('SET CHARACTER SET utf8');
    } catch (Exception $e) {
        echo '<h1>Service unavailable</h1>';
        echo '<br />';
        echo '<h2>Error Info :'.$e->getMessage().'</h2>';
        exit();
    }
}

//Check language
$load_language = new Language(SYSTEM_PATH);
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !isset($_COOKIE['language'])) {
    $browser_lang = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
    require ROOT_PATH.$load_language->loadBrowserLanguage($browser_lang);
    require ROOT_PATH.'/language/language.php';
} elseif (isset($_COOKIE['language'])) {
    require ROOT_PATH.$load_language->loadCookieLanguage($_COOKIE['language']);
    require ROOT_PATH.'/language/language.php';
} else {
    $browser_lang = 'en';
    require ROOT_PATH.'/language/language.php';
}

//Change language
if (isset($_GET['lang'])) {
    $load_language->setLanguage($_GET['lang']);
    header('Location: '.$host['link'].$_SERVER['PHP_SELF'].$load_language->getLinks($_GET));
}

//Check session
if (!defined('SESSION_ID')) {
    echo '<h1>'.$lang_session_error.'</h1>';
    echo '<br />';
    echo '<h2>'.$lang_please.' <a href="./install" style="color: blue;">'.$lang_reinstall.'</a> '.$lang_messageboard.' !</h2>';
    define('SESSION_ID', 'sessionerror');
    exit();
} else {
    session_name(SESSION_ID);
    session_start();
}

//Check login user
if (!empty($_SESSION['username'])) {
    $login['username'] = $_SESSION['username'];
    $admin_query = 'SELECT id,is_admin FROM user WHERE username = ?';
    $admin_stmt = $conn->stmt_init();

    if ($admin_stmt->prepare($admin_query)) {
        $admin_stmt->bind_param('s', $login['username']);
        $admin_stmt->execute();
        $admin_stmt->bind_result($id, $is_admin);
        $admin_result = $admin_stmt->get_result();
        while ($admin_row = $admin_result->fetch_assoc()) {
            $admin_check = $admin_row['is_admin'];
            $login['uid'] = $admin_row['id'];
        }
        if ($admin_check === 'true') {
            $login['admin'] = true;
        } else {
            $login['admin'] = false;
        }
    }
} else {
    $login['admin'] = false;
}

/* MetaInfo */
$metainfo = new MetaTag($conn);

//OpenGraph site name and url
$meta['name'] = $metainfo->getMainName();
$meta['url'] = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Meta Title
if (isset($_GET['aid'])) {
    $meta['title'] = $metainfo->getArticleTitle($_GET['aid']);
} elseif (isset($_GET['bid'])) {
    $meta['title'] = $metainfo->getBoardTitle($_GET['bid']);
} else {
    $meta['title'] = $metainfo->getMainName();
}

//Meta Description
if (isset($_GET['aid'])) {
    $meta['description'] = $metainfo->getArticleDescription($_GET['aid']);
} elseif (isset($_GET['bid'])) {
    $meta['description'] = $metainfo->getBoardDescription($_GET['bid']);
} else {
    $meta['description'] = $metainfo->getMainDescription();
}
