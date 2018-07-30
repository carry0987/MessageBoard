<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template');
$load->loadFunction('filter', 'bbcode');

//Template setting
$options = array(
    'template_dir' => 'template/common/',
    'css_dir' => 'static/css/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/',
    'auto_update' => true,
    'cache_lifetime' => 0,
);

$template = Template::getInstance();
$template->setOptions($options);

if (isset($_GET['aid'])) {
    if ($_GET['aid'] == '') {
        header('Location: ./');
        exit();
    } else {
        $article_id = input_filter($_GET['aid']);
    }
} else {
    $article_id = 1;
}


//Prepare Article Info
$query = 'SELECT id,user_id,title,content,board_id,post_date FROM article WHERE id = ?';
$stmt = $conn->stmt_init();

//Prepare author total article
$author_article_query = 'SELECT id FROM article WHERE user_id = ?';
$author_article_stmt = $conn->stmt_init();

//Prepare author information
$author_info_query = 'SELECT username,is_admin,join_date FROM user WHERE id = ?';
$author_info_stmt = $conn->stmt_init();

//Get category, board, article result
if ($stmt->prepare($query)) {
    $stmt->bind_param('i', $article_id);
    $stmt->execute();
    $stmt->bind_result($id, $user_id, $title, $content, $board_id, $post_date);
    $result = $stmt->get_result();
    if ($result->num_rows != 0) {
        $show_article = true;
        while ($row = $result->fetch_assoc()) {
            $date_format = 'Y-m-d';
            $post_date = date($date_format, strtotime($row['post_date']));
            //Get author total article
            if ($author_article_stmt->prepare($author_article_query)) {
                $author_article_stmt->bind_param('i', $row['user_id']);
                $author_article_stmt->execute();
                $author_article_stmt->store_result();
                if ($author_article_stmt->num_rows != 0) {
                    $author_total_article = $author_article_stmt->num_rows;
                } else {
                    $author_total_article = 0;
                }
                $author_article_stmt->free_result();
            }
            //Get author informarion
            if ($author_info_stmt->prepare($author_info_query)) {
                $author_info_stmt->bind_param('i', $row['user_id']);
                $author_info_stmt->execute();
                $author_info_stmt->bind_result($username, $is_admin, $join_date);
                $author_info_result = $author_info_stmt->get_result();
                $author_info_row = $author_info_result->fetch_assoc();
                $author_join_date = date($date_format, strtotime($author_info_row['join_date']));
                $author_info_stmt->free_result();
            }
            $article_title = $row['title'];
            $article_author_id = $row['user_id'];
            $article_author = $author_info_row['username'];
            $article_admin = $author_info_row['is_admin'];
            $article_content = bbcode2html($row['content']);
            $board_query = 'SELECT id,board_name,category_id FROM board WHERE id = ?';
            $board_stmt = $conn->stmt_init();
        if ($board_stmt->prepare($board_query)) {
            $board_stmt->bind_param('i', $row['board_id']);
            $board_stmt->execute();
            $board_stmt->bind_result($id, $board_name,$category_id);
            $board_result = $board_stmt->get_result();
            while ($board_row = $board_result->fetch_assoc()) {
                $board_link = $board_row['id'];
                $board_from = $board_row['board_name'];
                $category_query = 'SELECT category_name FROM category WHERE id = ?';
                $category_stmt = $conn->stmt_init();
                if ($category_stmt->prepare($category_query)) {
                    $category_stmt->bind_param('i', $board_row['category_id']);
                    $category_stmt->execute();
                    $category_stmt->bind_result($category_name);
                    $category_result = $category_stmt->get_result();
                    while ($category_row = $category_result->fetch_assoc()) {
                        $category_from = $category_row['category_name'];
                    }
                }
            }
        }
        }
        //Breadcrumb
        $article_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}

//Load Template
include($template->loadTemplate('header_common.html'));
include($template->loadTemplate('view_article.html'));
include($template->loadTemplate('footer_common.html'));
