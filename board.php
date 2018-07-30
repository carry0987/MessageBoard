<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'page', 'data_create');
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

//Get url for breadcrumb
$board_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Check URL
if (!empty($_GET['bid']) && ctype_digit($_GET['bid'])) {
    $board_id = input_filter($_GET['bid']);

    //Create article
    if (isset($_GET['action']) && $_GET['action'] === 'create_article') {
        if (!empty($login['username'])) {
            $create_post = true;
            $display = 'view_create';
        } else {
            $create_post = false;
            $display = 'view_login';
        }
    } else {
        $create_post = false;
        $display = 'view_board';
    }

    //Check create article
    if ($create_post === true && isset($_POST['submit'])) {
        $create_permit = true;

        if (empty($_POST['title']) || empty($_POST['content'])) {
            $create_permit = false;
            $display = 'view_error';
        }

        if ($create_permit !== false) {
            $create_time = date('Y-m-d H:i:s');
            $article_info = array(
                'user_id' => $login['uid'],
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'board_id' => $board_id,
                'set_sitemap' => 'true',
                'last_edit' => $create_time,
                'post_date' => $create_time
            );
            $create_article = new DataCreate($conn);
            $create_article->createArticle($article_info);
            $display = 'view_success';
        } else {
            $display = 'view_error';
        }
    }

    //Prepare to get board name
    $board_query = 'SELECT board_name,board_description FROM board WHERE id = ?';
    $board_stmt = $conn->stmt_init();

    //Board info
    if ($board_stmt->prepare($board_query)) {
        $board_stmt->bind_param('i', $board_id);
        $board_stmt->execute();
        $board_stmt->bind_result($board_name, $board_description);
        $board_result = $board_stmt->get_result();
        if ($board_result->num_rows != 0) {
            $show_board = true;
            while ($board_row = $board_result->fetch_assoc()) {
                $board_name = $board_row['board_name'];
                $board_description = $board_row['board_description'];
            }
        } else {
            $show_board = false;
        }
    } else {
        header('Location: '.$base_url.'');
        exit();
    }

    if (!empty($_GET['page']) && ctype_digit($_GET['page'])) {
        if ($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
            header('Location: '.$base_url.'/board.php?bid='.$board_id.'');
        } else {
            $current_page = input_filter($_GET['page']);
        }
    } else {
        $current_page = 1;
    }
} else {
    header('Location: '.$base_url.'');
}

//Page Script
$page_query = 'SELECT id,user_id,title,board_id,post_date FROM article WHERE board_id = ? ORDER BY id DESC';
$page_stmt = $conn->stmt_init();
$rows = array();
//Check page result
$page_stmt->prepare($page_query);
$page_stmt->bind_param('i', $board_id);
$page_stmt->execute();
$page_stmt->bind_result($id, $user_id, $title, $board_id, $post_date);
$page_result = $page_stmt->get_result();
if ($page_result->num_rows != 0) {
    $show_page = true;
    //Get page info
    while ($page_row = $page_result->fetch_assoc()) {
        $user_query = 'SELECT username FROM user WHERE id = ?';
        $user_stmt = $conn->stmt_init();
        //Get user info
        $user_stmt->prepare($user_query);
        $user_stmt->bind_param('i', $page_row['user_id']);
        $user_stmt->execute();
        $user_stmt->bind_result($username);
        $user_result = $user_stmt->get_result();
        if ($user_result->num_rows != 0) {
            $show_user = true;
            while ($user_row = $user_result->fetch_assoc()) {
                $article[] = array(
                    'id' => $page_row['id'],
                    'user_id' => $page_row['user_id'],
                    'title' => $page_row['title'],
                    'date' => $page_row['post_date'],
                    'username' => $user_row['username'],
                );
            }
        } else {
            $show_user = false;
        }
    }
} else {
    $show_page = false;
}

if (isset($article) && count($article) != 0) {
    $pagination = new Pagination($article, $current_page, 10);
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

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));

//Close database connect
$board_stmt->free_result();
$conn->close();
