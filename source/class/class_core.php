<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/class_database.php';
require dirname(__FILE__).'/class_language.php';
require dirname(__FILE__).'/class_metatag.php';
require dirname(__FILE__).'/class_remember_me.php';

//Set ROOT_PATH
define('ROOT_PATH', dirname(__FILE__).'/../../');
$host['link'] = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'];

//Check config
if (file_exists(ROOT_PATH.'/config/config_global.php') === false) {
    if (file_exists(ROOT_PATH.'/install/index.php')) {
        if (file_exists(ROOT_PATH.'/config/installed.lock') === false && file_exists(ROOT_PATH.'/install/installed.lock') === false) {
            header('Location: ./install');
        } else {
            echo '<h1>Program installed but could not find the config file !</h1>'."\n";
            echo '<h2>Please put config_global.php file to "config" folder </h2>'."\n";
            echo '<h3>If you want to reinstall MessageBoard, just remove "installed.lock" file from "config" &amp; "install" folder</h3>'."\n";
            echo '<h3>Then go to install page</h3>'."\n";
        }
    } else {
            echo '<h1>Could not find the config file !</h1>'."\n";
            echo '<h2>Please put config_global.php file to "config" folder </h2>';
    }
    exit();
} else {
    require ROOT_PATH.'/config/config_global.php';
    require ROOT_PATH.'/source/version.php';
    $base_url = $host['link'].SYSTEM_PATH;
    $database = Database::getInstance();
    $conn = $database->getConnection();
}

//Check language
$SYSTEM = array();
$load_language = new Language(SYSTEM_PATH);
$load_language->setLanguageFile(array('common', 'database', 'member', 'admin', '404', 'file'));
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !isset($_COOKIE['language'])) {
    $SYSTEM['system_lang'] = strtok(strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','), '-');
    $lang_file = $load_language->loadLanguage($SYSTEM['system_lang']);
    foreach ($lang_file as $lang) {
        require ROOT_PATH.$lang;
    }
} elseif (isset($_COOKIE['language'])) {
    $lang_file = $load_language->loadLanguage($_COOKIE['language']);
    $SYSTEM['system_lang'] = $_COOKIE['language'];
    foreach ($lang_file as $lang) {
        require ROOT_PATH.$lang;
    }
} else {
    $SYSTEM['system_lang'] = $load_language->getWebLanguage($conn);
    $lang_file = $load_language->loadLanguage($SYSTEM['system_lang']);
    foreach ($lang_file as $lang) {
        require ROOT_PATH.$lang;
    }
}

//Change language
if (isset($_POST['lang'])) {
    $load_language->setLanguage($_POST['lang'], (isset($_SERVER['HTTPS'])?true:false));
} elseif (isset($_GET['lang'])) {
    $load_language->setLanguage($_GET['lang'], (isset($_SERVER['HTTPS'])?true:false));
}

//Check session
if (!defined('SESSION_ID')) {
    echo '<h1>'.$LANG['common']['session_error'].'</h1>';
    echo '<br />';
    echo '<h2>'.$LANG['common']['please'].' <a href="./install" style="color: blue;">'.$LANG['common']['reinstall'].'</a> '.$LANG['common']['messageboard'].' !</h2>';
    define('SESSION_ID', 'sessionerror');
    exit();
} else {
    session_name(SESSION_ID);
    session_start();
}

//Check login user
if (!empty($_SESSION['username'])) {
    $login['username'] = $_SESSION['username'];
    $admin_query = 'SELECT uid,is_admin,banned FROM user WHERE username = ?';
    $admin_stmt = $conn->stmt_init();
    if ($admin_stmt->prepare($admin_query)) {
        $admin_stmt->bind_param('s', $login['username']);
        $admin_stmt->execute();
        $admin_stmt->bind_result($uid, $is_admin, $banned);
        $admin_result = $admin_stmt->get_result();
        while ($admin_row = $admin_result->fetch_assoc()) {
            $admin_check = $admin_row['is_admin'];
            $banned_check = $admin_row['banned'];
            $login['uid'] = $admin_row['uid'];
        }
        if ($admin_check === 1) {
            $login['admin'] = true;
        } else {
            $login['admin'] = false;
        }
        if ($banned_check === 1) {
            $login['banned'] = true;
        } else {
            $login['banned'] = false;
        }
    }
} elseif (!empty($_COOKIE['user_login']) && !empty($_COOKIE['random_pw']) && !empty($_COOKIE['random_selector'])) {
    $rememberMe = new RememberMe(SYSTEM_PATH);
    $rememberMe->getConnection($conn);
    $checkRemember = $rememberMe->checkUserInfo($_COOKIE['user_login'], $_COOKIE['random_selector'], $_COOKIE['random_pw']);
    if ($checkRemember !== false) {
        $login['uid'] = $_COOKIE['user_login'];
        $_SESSION['username'] = $checkRemember['username'];
        $login['username'] = $_SESSION['username'];
        $admin_query = 'SELECT uid,is_admin,banned FROM user WHERE username = ?';
        $admin_stmt = $conn->stmt_init();
        if ($admin_stmt->prepare($admin_query)) {
            $admin_stmt->bind_param('s', $login['username']);
            $admin_stmt->execute();
            $admin_stmt->bind_result($uid, $is_admin, $banned);
            $admin_result = $admin_stmt->get_result();
            while ($admin_row = $admin_result->fetch_assoc()) {
                $admin_check = $admin_row['is_admin'];
                $banned_check = $admin_row['banned'];
                $login['uid'] = $admin_row['uid'];
            }
            if ($admin_check === 1) {
                $login['admin'] = true;
            } else {
                $login['admin'] = false;
            }
            if ($banned_check === 1) {
                $login['banned'] = true;
            } else {
                $login['banned'] = false;
            }
        }
    } else {
        $login['username'] = false;
        $login['uid'] = false;
        $login['admin'] = false;
        $login['banned'] = false;
    }
} else {
    $login['username'] = false;
    $login['uid'] = false;
    $login['admin'] = false;
    $login['banned'] = false;
}

/* MetaInfo */
$metainfo = MetaTag::getInstance();
$metainfo->getConnection($conn);

//Meta site name and url
$meta['name'] = $metainfo->getMainName();
$meta['url'] = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Meta Title
if (isset($_GET['aid'])) {
    $meta['title'] = $metainfo->getArticleTitle($_GET['aid']);
} elseif (isset($_GET['bid'])) {
    $meta['title'] = $metainfo->getBoardTitle($_GET['bid']);
} elseif (isset($_GET['cid'])) {
    $meta['title'] = $metainfo->getCategoryTitle($_GET['cid']);
} else {
    $meta['current_url'] = basename($_SERVER['SCRIPT_FILENAME'], '.php');
    switch ($meta['current_url']) {
        case 'login' === $meta['current_url']:
            $meta['title'] = $LANG['common']['login'].' | '.$metainfo->getMainName();
            break;
        case 'signup' === $meta['current_url']:
            $meta['title'] = $LANG['common']['signup'].' | '.$metainfo->getMainName();
            break;
        case 'logout' === $meta['current_url']:
            $meta['title'] = $LANG['common']['logout'].' | '.$metainfo->getMainName();
            break;
        default:
            $meta['title'] = $metainfo->getMainName();
            break;
    }
}

//Meta Description
if (isset($_GET['aid'])) {
    $meta['description'] = $metainfo->getArticleDescription($_GET['aid']);
    $meta['type'] = 'article';
} elseif (isset($_GET['bid'])) {
    $meta['description'] = $metainfo->getBoardDescription($_GET['bid']);
    $meta['type'] = 'website';
} elseif (isset($_GET['cid'])) {
    $meta['description'] = $metainfo->getCategoryDescription($_GET['cid']);
    $meta['type'] = 'website';
} else {
    $meta['description'] = $metainfo->getMainDescription();
    $meta['type'] = 'website';
}

//Get Timezone
require dirname(__FILE__).'/class_timezone.php';
$timezone = Timezone::getInstance();
$timezone->getConnection($conn);
$SYSTEM['system_timezone'] = $timezone->getWebTimezone();
if (isset($login['uid']) && $timezone->getCustomTimezone($login['uid']) !== false) {
    $SYSTEM['user_timezone'] = $timezone->getCustomTimezone($login['uid']);
} else {
    $SYSTEM['user_timezone'] = false;
}
