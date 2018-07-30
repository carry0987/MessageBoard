<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template');
$load->loadFunction('filter');

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

//Breadcrumb
$index_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Category list
$category_query = 'SELECT id,category_name,category_description FROM category ORDER BY id ASC';
$category_stmt = $conn->stmt_init();
$categorys = array();

if ($category_stmt->prepare($category_query)) {
    $category_stmt->execute();
    $category_stmt->bind_result($id, $category_name, $category_description);
    $category_result = $category_stmt->get_result();
    if ($category_result->num_rows != 0) {
        while ($category_row = $category_result->fetch_assoc()) {
            $category_row['boards'] = array();
            $board_query = 'SELECT id,board_name FROM board WHERE category_id = ? ORDER BY id ASC';
            $board_stmt = $conn->stmt_init();
            if ($board_stmt->prepare($board_query)) {
                $board_stmt->bind_param('i', $category_row['id']);
                $board_stmt->execute();
                $board_stmt->bind_result($id, $board_name);
                $board_result = $board_stmt->get_result();
                if ($board_result->num_rows != 0) {
                    $show_board_list = true;
                    while ($board_row = $board_result->fetch_assoc()) {
                        $total_article_query = 'SELECT id,user_id,title,post_date FROM article WHERE board_id = ?';
                        $total_article_stmt = $conn->stmt_init();
                        if ($total_article_stmt->prepare($total_article_query)) {
                            $total_article_stmt->bind_param('i', $board_row['id']);
                            $total_article_stmt->execute();
                            $total_article_stmt->bind_result($id, $user_id, $title ,$post_date);
                            $total_article_result = $total_article_stmt->get_result();
                            if ($total_article_result->num_rows != 0) {
                                $board_row['articles'] = array();
                                while ($total_article_row = $total_article_result->fetch_assoc()) {
                                    $board_row['articles'][] = $total_article_row;
                                }
                            }
                            //Get latest article
                            $latest_query = 'SELECT id,user_id,title,post_date FROM article WHERE board_id = ? ORDER BY id DESC LIMIT 1';
                            $latest_stmt = $conn->stmt_init();
                            if ($latest_stmt->prepare($latest_query)) {
                                $latest_stmt->bind_param('i', $board_row['id']);
                                $latest_stmt->execute();
                                $latest_stmt->bind_result($id, $user_id, $title ,$post_date);
                                $latest_result = $latest_stmt->get_result();
                                if ($latest_result->num_rows != 0) {
                                    $board_row['latest'] = array();
                                    while ($latest_row = $latest_result->fetch_assoc()) {
                                        $board_row['latest'] = $latest_row;
                                        $date_format = 'Y-m-d';
                                        $latest_username_query = 'SELECT username FROM user WHERE id = ?';
                                        $latest_username_stmt = $conn->stmt_init();
                                        if ($latest_username_stmt->prepare($latest_username_query)) {
                                            $latest_username_stmt->bind_param('i', $board_row['latest']['user_id']);
                                            $latest_username_stmt->execute();
                                            $latest_username_stmt->bind_result($username);
                                            $latest_username_result = $latest_username_stmt->get_result();
                                            if ($latest_username_result->num_rows != 0) {
                                                while ($latest_username_row = $latest_username_result->fetch_assoc()) {
                                                    $board_row['latest_user'] = $latest_username_row;
                                                }
                                            }
                                        }
                                    }
                                }
                                $category_row['boards'][] = $board_row;
                            }
                        }
                    }
                }
                $categorys[] = $category_row;
            }
        }
    }
}

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate('view_index.html'));
include($template->loadTemplate('footer_common.html'));
