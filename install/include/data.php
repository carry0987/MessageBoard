<?php
header('content-type:text/html;charset=utf-8');
define('IN_INSTALL', true);
require dirname(__FILE__).'/data.check.php';
require dirname(__FILE__).'/install_data_array.php';

//Get session id
$session_id = generateSessionID();
$config = <<<EOT
<?php
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASSWORD', '$db_password');
define('DB_NAME', '$db_name');
define('DB_PORT', '$db_port');
define('SYSTEM_PATH', '$get_path');
define('SESSION_ID', '$session_id');

EOT;

//Set root path
define('CONFIG_ROOT', dirname(dirname(dirname(__FILE__))));
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//Handle Exception of MySQLi
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ALL;

try {
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);
    if (!empty($conn->connect_errno)) {
        $data_check = false;
        if ($conn->connect_errno === 1049) {
            echo showMsg($LANG['database']['db_connect_error'], $LANG['database']['db_unknown'].': \''.$db_name.'\'');
        } else {
            echo showMsg($LANG['database']['db_connect_error'], $conn->connect_error);
        }
        echo '<h2><a href="../" style="color: blue;">'.$LANG['common']['back_page'].'</a></h2>';
        exit();
    } else {
        $conn->set_charset('utf8mb4');
    }
    if (file_exists(CONFIG_ROOT.'/install/installed.lock') === true || file_exists(CONFIG_ROOT.'/config/installed.lock') === true) {
        $data_check = false;
        echo '<h1>'.$LANG['install']['installed'].'</h1>';
        echo '<br/>';
        echo '<h2><a href="../" style="color: blue;">'.$LANG['common']['back_to'].$LANG['common']['index'].'</a></h2>';
        exit();
    }
} catch (mysqli_sql_exception $e) {
    echo '<h1>Service unavailable</h1>'."\n";
    echo '<br/>';
    echo '<h2>Error Info :'.$e->getMessage().'</h2>';
    exit();
}

//Set Database
if ($data_check === true) {
    try {
        if (file_exists(CONFIG_ROOT.'/install/data/data.sql') === true) {
            $sql = createDatabaseTable($conn, CONFIG_ROOT.'/install/data/data.sql');
            if ($sql !== true) {
                echo $sql;
                exit();
            }
        } else {
            echo showMsg($LANG['common']['file_not_exist'], $LANG['common']['file_not_found'].': \''.'/install/data/data.sql'.'\'');
            exit();
        }
    } catch (Exception $e) {
        echo '<h1>Service unavailable</h1>'."\n";
        echo '<br/>';
        echo '<h2>Error Info :'.$e->getMessage().'</h2>';
        exit();
    }
} else {
    echo '<h1>'.$LANG['common']['input_empty'].'</h1>'.'<br />';
    echo '<a href="../" style="color: blue;">Back</a>';
    exit();
}

//Data from /install/include/install_data_array.php
try {
    $user_stmt = $conn->stmt_init();
    $user_stmt->prepare($user_query);
    $user_stmt->bind_param('sssssiiss', 
        $admin_display_name, 
        $admin_username, 
        $set_admin_psw, 
        $user_email, 
        $default_language, 
        $insert[0], 
        $insert[9], 
        $get_time, 
        $get_time
    );
    $user_stmt->execute();
    $category_stmt = $conn->stmt_init();
    $category_stmt->prepare($category_query);
    $category_stmt->bind_param('sssss', $insert[6], $insert[7], $insert[0], $get_time, $get_time);
    $category_stmt->execute();
    $board_stmt = $conn->stmt_init();
    $board_stmt->prepare($board_query);
    $board_stmt->bind_param('ssisss', $insert[4], $insert[5], $insert[0], $insert[0], $get_time, $get_time);
    $board_stmt->execute();
    $email_stmt = $conn->stmt_init();
    $email_stmt->prepare($email_config_query);
    $email_stmt->bind_param('iis', $insert[0], $get_email_set, $insert[10]);
    $email_stmt->execute();
    $global_stmt = $conn->stmt_init();
    $global_stmt->prepare($global_config_query);
    $global_stmt->bind_param('isssss', $insert[0], $insert[8], $insert[5], $web_lang, $web_timezone, $insert[12]);
    $global_stmt->execute();
    $config_stmt = $conn->stmt_init();
    foreach ($config_query as $query_key) {
        $config_stmt->prepare($query_key['config']);
        $config_stmt->bind_param($query_key['prepare'], ...$query_key['bind']);
        $config_stmt->execute();
    }
} catch (mysqli_sql_exception $e) {
    $install_failed = true;
    echo '<h1>Service unavailable</h1>'."\n";
    echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
    echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
    echo '<h3>Error File :'.$e->getFile().'</h3>'."\n";
    echo '<h3>Error Line :'.$e->getLine().'</h3>'."\n";
    echo '<h2>'.$LANG['common']['please'].' <a href="../../install" style="color: blue;">'.$LANG['common']['reinstall'].'</a> '.$LANG['common']['messageboard'].' !</h2>';
    exit();
}

if (!isset($install_failed) || $install_failed !== true) {
    $config_file = generateFile(CONFIG_ROOT.'/config/config_global.php', $config);
    if ($config_file !== true) {
        echo $config_file;
        exit();
    }
    $generate_lock_file['0'] = generateFile(CONFIG_ROOT.'/install/installed.lock', '');
    $generate_lock_file['1'] = generateFile(CONFIG_ROOT.'/config/installed.lock', '');
    foreach ($generate_lock_file as $generate_lock) {
        if ($generate_lock !== true) {
            echo $generate_lock;
            exit();
        }
    }
    echo '<h1>'.$LANG['install']['install_success'].'</h1>'."\n";
    echo '<h2>'.$LANG['install']['install_remove'].'</h2>'."\n";
    echo '<h3><a href="../../" style="color: blue;">Go !</a></h3>';
}
