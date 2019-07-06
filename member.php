<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'pagination');
$load->loadFunction('filter', 'core', 'bbcode');

//Template setting
$options = array(
    'template_dir' => 'template/member/',
    'css_dir' => 'static/css/member/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/member/',
    'auto_update' => true,
    'cache_lifetime' => 0,
    'cache_db' => $conn
);

$template = Template::getInstance();
$template->setOptions($options);
//Check page value
$currentPage = (!empty($_GET['page'])) ? input_filter(checkPage($_GET['page'])) : 1;

//Breadcrumb
$member_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Prepare to get user info
$member_query = 'SELECT display_name,username,bio,is_admin,online_status,last_login,join_date FROM user WHERE uid = ?';
$member_stmt = $conn->stmt_init();

//Member info
if (!empty($_GET['uid'])) {
    if ($member_stmt->prepare($member_query)) {
        $member_stmt->bind_param('i', $_GET['uid']);
        $member_stmt->execute();
        $member_stmt->bind_result($display_name, $username, $bio, $is_admin, $online_status, $last_login, $join_date);
        $member_result = $member_stmt->get_result();
        if ($member_result->num_rows != 0) {
            $show_member = true;
            $display = 'view_member';
            $member['row'] = $member_result->fetch_assoc();
            $member_bio = input_filter($member['row']['bio']);
            $member_last_login = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $member['row']['last_login'], 'Y-m-d');
            $member_join_date = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $member['row']['join_date'], 'Y-m-d');
        } else {
            $show_member = false;
            header('HTTP/1.0 404 Not Found');
            include($template->loadTemplate('empty_member.html'));
            exit();
        }
    } else {
        header('Location: '.$base_url.'');
        exit();
    }
} else {
    header('Location: '.$base_url.'');
    exit();
}

//Prepare to get article list
if (isset($login['uid']) && $login['uid'] === (int) $_GET['uid']) {
    $article_query = 'SELECT aid,title,post_date FROM article WHERE user_id = ? ORDER BY aid DESC';
    $article_bind = 'i';
    $article_param = array($_GET['uid']);
} else {
    $articleProperty = 0;
    $article_query = 'SELECT aid,title,post_date FROM article WHERE user_id = ? AND property = ? ORDER BY aid DESC';
    $article_bind = 'ii';
    $article_param = array($_GET['uid'], $articleProperty);
}
$article_stmt = $conn->stmt_init();
$article = array();

//Check article result
$article_stmt->prepare($article_query);
$article_stmt->bind_param($article_bind, ...$article_param);
$article_stmt->execute();
$article_stmt->bind_result($aid, $title, $post_date);
$article_result = $article_stmt->get_result();
if ($article_result->num_rows != 0) {
    $show_article = true;
    $article_info['article_num'] = $article_result->num_rows;
    while ($article_row = $article_result->fetch_assoc()) {
        $article[] = array(
            'aid' => $article_row['aid'],
            'title' => $article_row['title'],
            'post_date' => getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $article_row['post_date'], 'Y-m-d H:i')
        );
    }
} else {
    $show_article = false;
    $article_info['article_num'] = 0;
}

if ($show_article === true) {
    $itemsPerPage = 10;
    $urlPattern = 'member.php?uid='.$_GET['uid'].'&amp;page=(:num)';
    $paginator = new Pagination($article, $itemsPerPage, $currentPage, $urlPattern);
    $article_result = $paginator->getResults();
    $total_article = $paginator->getTotalItems();
    $total_pages = $paginator->getNumPages();
} else {
    $total_article = 0;
    $total_pages = 0;
}

if (is_array($article_result)) {
    if (isset($article_result) && count($article_result) == 0) {
        header('HTTP/1.0 404 Not Found');
    }
}

include($template->loadTemplate('header_member.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_member.html'));
