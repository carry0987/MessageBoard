<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'page');
$load->loadFunction('filter', 'bbcode');

//Template setting
$options = array(
    'template_dir' => 'template/member/',
    'css_dir' => 'static/css/member/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/member/',
    'auto_update' => true,
    'cache_lifetime' => 0,
);

$template = Template::getInstance();
$template->setOptions($options);

if (!empty($_GET['page']) && ctype_digit($_GET['page'])) {
    if ($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
        header('Location: '.$base_url.'/member.php?uid='.$_GET['uid'].'');
    } else {
        $current_page = input_filter($_GET['page']);
    }
} else {
    $current_page = 1;
}

//Breadcrumb
$member_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Prepare to get user info
$member_query = 'SELECT username,is_admin,last_login,join_date FROM user WHERE id = ?';
$member_stmt = $conn->stmt_init();

//Member info
if (!empty($_GET['uid'])) {
    if ($member_stmt->prepare($member_query)) {
        $member_stmt->bind_param('i', $_GET['uid']);
        $member_stmt->execute();
        $member_stmt->bind_result($username, $is_admin, $last_login, $join_date);
        $member_result = $member_stmt->get_result();
        if ($member_result->num_rows != 0) {
            $display = 'view_member';
            while ($member_row = $member_result->fetch_assoc()) {
                $member_name = $member_row['username'];
                $member_admin = $member_row['is_admin'];
                $member_last_login = date('Y-m-d', strtotime($member_row['last_login']));
                $member_join_date = date('Y-m-d', strtotime($member_row['join_date']));
            }
        } else {
            $display = 'member_empty';
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
$article_query = 'SELECT id,title,content,post_date FROM article WHERE user_id = ? ORDER BY id DESC';
$article_stmt = $conn->stmt_init();
$article = array();

//Check article result
$article_stmt->prepare($article_query);
$article_stmt->bind_param('i', $_GET['uid']);
$article_stmt->execute();
$article_stmt->bind_result($id, $title, $content, $post_date);
$article_result = $article_stmt->get_result();
if ($article_result->num_rows != 0) {
    $show_article = true;
    while ($article_row = $article_result->fetch_assoc()) {
        $article[] = array(
            'aid' => $article_row['id'],
            'title' => $article_row['title'],
            'content_preview' => stripValue(strip_tags(bbcode2html($article_row['content']))),
            'post_date' => $article_row['post_date']
        );
    }
} else {
    $show_article = false;
}

if (isset($article) && count($article) != 0) {
    $pagination = new Pagination($article, $current_page, 8);
    $pagination->setShowFirstAndLast(false);
    $article_pages = $pagination->getResults();
    $total_pages = $pagination->getTotalPages();
    $total_article = count($article);
} else {
    $total_article = '';
    $total_pages = '';
}

//Check result
if (isset($article_pages) && count($article_pages) != 0) {
    $page_numbers = $pagination->getLinks();
    $show_prev = $pagination->showPrev();
    $show_next = $pagination->showNext();
} else {
    $page_numbers = '';
    $show_prev = '';
    $show_next = '';
}

include($template->loadTemplate('header_member.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_member.html'));
