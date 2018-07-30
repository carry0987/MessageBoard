<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/session.php';
require dirname(__FILE__).'/data.check.php';

$config = <<<EOT
<?php
mysqli_report(MYSQLI_REPORT_STRICT);
date_default_timezone_set('Asia/Taipei'); //You can change timezone whatever you want

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

try {
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);
    $conn->query('SET CHARACTER SET utf8');
    $generate_file = fopen(CONFIG_ROOT.'/config/config_global.php', 'w');
    fwrite($generate_file, $config);
    fclose($generate_file);
} catch (Exception $e) {
    echo '<h1>Service unavailable</h1>';
    echo '<br />';
    echo '<h2>Error Info :'.$e->getMessage().'</h2>';
    exit();
}

//Set Database
if ($data_check === 'pass') {
    try {
        $sql = file_get_contents(CONFIG_ROOT.'/install/data/data.sql');
        $array = explode(';', $sql);
        foreach ($array as $value) {
            $conn->query($value.';');
        }
    } catch (Exception $e) {
        echo '<h2>Error Info :'.$e->getMessage().'</h2>';
        exit();
    }
} else {
    echo '<h1>'.$lang_install_empty.'</h1>'.'<br />';
    echo '<a href="../" style="color: blue;">Back</a>';
    exit();
}

//Insert default value
$user_query = 'INSERT INTO user (id, username, password, email, is_admin, last_login, join_date) VALUES (?,?,?,?,?,?,?)';
$user_stmt = $conn->stmt_init();

$category_query = 'INSERT INTO category (id, category_name, category_description, set_sitemap, create_date) VALUES (?,?,?,?,?)';
$category_stmt = $conn->stmt_init();

$board_query = 'INSERT INTO board (id, board_name, board_description, category_id, set_sitemap, create_date) VALUES (?,?,?,?,?,?)';
$board_stmt = $conn->stmt_init();

$article_query = 'INSERT INTO article (id, user_id, title, content, board_id, set_sitemap, last_edit, post_date) VALUES (?,?,?,?,?,?,?,?)';
$article_stmt = $conn->stmt_init();

$config_query = 'INSERT INTO config (id, web_name, web_description) VALUES (?,?,?)';
$config_stmt = $conn->stmt_init();

$recaptcha_query = 'INSERT INTO recaptcha (id, site_key, secret_key) VALUES (?,?,?)';
$recaptcha_stmt = $conn->stmt_init();

//Set value
$insert[0] = '1';
$insert[1] = 'Welcome';
$insert[2] = 'Welcome To Messageboard !';
$insert[3] = 'true';
$insert[4] = 'Default';
$insert[5] = 'This MessageBoard was made by carry0987';
$insert[6] = 'Default category';
$insert[7] = 'Messageboard';

try {
    $user_stmt->prepare($user_query);
    $user_stmt->bind_param('issssss', $insert[0], $admin_username, $set_admin_psw, $user_email, $insert[3], $get_time, $get_time);
    $user_stmt->execute();
    $category_stmt->prepare($category_query);
    $category_stmt->bind_param('issss', $insert[0], $insert[6], $insert[6], $insert[3], $get_time);
    $category_stmt->execute();
    $board_stmt->prepare($board_query);
    $board_stmt->bind_param('ississ', $insert[0], $insert[4], $insert[5], $insert[0], $insert[3], $get_time);
    $board_stmt->execute();
    $article_stmt->prepare($article_query);
    $article_stmt->bind_param('iississs', $insert[0], $insert[0], $insert[1], $insert[2], $insert[0], $insert[3], $get_time, $get_time);
    $article_stmt->execute();
    $config_stmt->prepare($config_query);
    $config_stmt->bind_param('iss', $insert[0], $insert[7], $insert[5]);
    $config_stmt->execute();
    $recaptcha_stmt->prepare($recaptcha_query);
    $recaptcha_stmt->bind_param('iss', $insert[0], $recaptcha_site, $recaptcha_secret);
    $recaptcha_stmt->execute();
    echo '<h1>'.$lang_install_success.'</h1>'."\n";
    echo '<h2><a href="../../" style="color: blue;">Go !</a></h2>';
} catch (Exception $e) {
    echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
    echo '<h2>'.$lang_please.' <a href="../../install" style="color: blue;">'.$lang_reinstall.'</a> '.$lang_messageboard.' !</h2>'."\n";
}
