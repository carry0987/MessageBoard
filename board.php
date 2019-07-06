<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'pagination', 'data_create', 'data_read', 'data_update', 'sitemap');
$load->loadFunction('filter', 'core', 'bbcode');

//Template setting
$options = array(
    'template_dir' => 'template/common/',
    'css_dir' => 'static/css/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/',
    'auto_update' => true,
    'cache_lifetime' => 0,
    'cache_db' => $conn
);

$template = Template::getInstance();
$template->setOptions($options);

//Get url for breadcrumb
$board_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Check URL
if (!empty($_GET['bid']) && ctype_digit($_GET['bid'])) {
    $board_id = (int) input_filter($_GET['bid']);
    //Create article
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'create_article') {
            if (!empty($login['username']) && !empty($login['uid'])) {
                if ($login['banned'] !== true) {
                    $create_post = true;
                    $display = 'view_create';
                } else {
                    $create_post = false;
                    $back['bid'] = $board_id;
                    $display = 'view_banned';
                }
            } else {
                $create_post = false;
                $_SESSION['login_bid'] = $board_id;
                header('Location: login.php');
            }
        } elseif ($_GET['action'] === 'update') {
            if (isset($_POST['pinned_sort'])) {
                $updateSort = DataUpdate::getInstance();
                $updateSort->getConnection($conn);
                if (is_array($_POST['pinned_sort'])) {
                    foreach ($_POST['pinned_sort'] as $key => $value) {
                        $updateData['pinned_sort'] = $key;
                        $sortResult = $updateSort->updatePinnedSort($value, $updateData);
                    }
                }
                $sortResult = ($sortResult === false) ?: true;
                exit($sortResult);
            }
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
            $back_page = 'board.php?bid='.$board_id;
            $display = 'view_input_error';
        }
        if ($create_permit !== false) {
            $create_time = time();
            $article_info = array(
                'user_id' => $login['uid'],
                'title' => $_POST['title'],
                'content' => bbcode2html($_POST['content']),
                'article_description' => stripValue(strip_tags(bbcode2html($_POST['content'])), 350),
                'board_id' => $board_id,
                'set_sitemap' => 1,
                'property' => $_POST['property'],
                'last_edit' => $create_time,
                'post_date' => $create_time
            );
            $create_article = DataCreate::getInstance();
            $create_article->getConnection($conn);
            $article_id = $create_article->createArticle($article_info);
            if (isset($_POST['pinned'])) {
                $articlePinned['pinned_sort'] = 1;
                $articlePinned['apply'] = serialize(array($board_id));
                if ($article_id !== 0 && $article_id !== false) {
                    $create_article->createArticlePinned($article_id, $articlePinned);
                }
            }
            $sitemapConfig = DataRead::getInstance();
            $sitemapConfig->getConnection($conn);
            $read_query = array(
                'config' => 'SELECT enable,auto_update,sitemap_path FROM seo_sitemap_config WHERE id = ?'
            );
            $seo['sitemap'] = $sitemapConfig->getConfig($read_query);
            if ($seo['sitemap'] !== false) {
                if ($seo['sitemap']['enable'] === 1 && $seo['sitemap']['auto_update'] === 1) {
                    $sitemap['path'] = $seo['sitemap']['sitemap_path'];
                    $sitemap['timezone'] = $SYSTEM['system_timezone'];
                    $sitemap['path'] = ltrim($sitemap['path'], '/\\');
                    if (file_exists(dirname(__FILE__).'/'.$sitemap['path'])) {
                        $sitemapArray = array(
                            $sitemapConfig->getSitemapArray('article', 'aid', $base_url, $article_id)
                        );
                        generateSitemap($sitemapArray, $sitemap, true);
                    } else {
                        $home_page[] = array('loc' => $base_url.'/', 'lastmod' => time(), 'changefreq' => 'always', 'priority' => '1.0');
                        $sitemapArray = array(
                            $home_page,
                            $sitemapConfig->getSitemapArray('category', 'cid', $base_url),
                            $sitemapConfig->getSitemapArray('board', 'bid', $base_url),
                            $sitemapConfig->getSitemapArray('article', 'aid', $base_url)
                        );
                        generateSitemap($sitemapArray, $sitemap, false);
                    }
                }
            }
            $display = 'view_success';
        } else {
            $back_page = 'board.php?bid='.$board_id;
            $display = 'view_input_error';
        }
    }

    //Prepare to get board name
    $board_query = 'SELECT name,description,category_id FROM board WHERE bid = ?';
    $board_stmt = $conn->stmt_init();

    //Board & Category info
    if ($board_stmt->prepare($board_query)) {
        $board_stmt->bind_param('i', $board_id);
        $board_stmt->execute();
        $board_stmt->bind_result($name, $description, $category_id);
        $board_result = $board_stmt->get_result();
        if ($board_result->num_rows != 0) {
            $show_board = true;
            while ($board_row = $board_result->fetch_assoc()) {
                $board_name = $board_row['name'];
                $board_description = $board_row['description'];
                $category_id = $board_row['category_id'];
                $category_query = 'SELECT name FROM category WHERE cid = ?';
                $category_stmt = $conn->stmt_init();
                if ($category_stmt->prepare($category_query)) {
                    $category_stmt->bind_param('i', $board_row['category_id']);
                    $category_stmt->execute();
                    $category_stmt->bind_result($name);
                    $category_result = $category_stmt->get_result();
                    while ($category_row = $category_result->fetch_assoc()) {
                        $category_name = $category_row['name'];
                    }
                }
            }
        } else {
            $show_board = false;
            header('HTTP/1.0 404 Not Found');
        }
    } else {
        header('Location: '.$base_url.'');
        exit();
    }
    //Check page value
    if (!empty($_GET['page']) && ctype_digit($_GET['page'])) {
        if ($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
            header('Location: '.$base_url.'/board.php?bid='.$board_id);
        } else {
            $current_page = input_filter($_GET['page']);
        }
    } else {
        $current_page = 1;
    }
} else {
    header('Location: '.$base_url.'');
}

//Get pinned article
$pinnedConfig = DataRead::getInstance();
$pinnedConfig->getConnection($conn);
$pinned_result = $pinnedConfig->getArticlePinned($board_id);
foreach ($pinned_result as $key => $value) {
    $pinnedReply = $pinnedConfig->getLastReply($value['aid']);
    $pinnedReplyInfo = $pinnedConfig->getReplyByArticle($value['aid']);
    $pinnedReplyInfo = $pinnedReplyInfo['count'];
    $pinned_result[$key]['show_reply'] = ($pinnedReply !== false) ? true : false;
    $pinned_result[$key]['reply_page'] = countTotalPage($pinnedReplyInfo + 1);
    $pinned_result[$key]['reply_id'] = $pinnedReply['reply_id'];
    $pinned_result[$key]['reply_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $pinnedReply['reply_date'], 'Y-m-d H:i');
    $pinned_result[$key]['total_reply'] = $pinnedReplyInfo;
    $pinned_result[$key]['total_comment'] = $pinnedConfig->getCommentByArticle($value['aid']);
}
if ($pinned_result !== false) {
    $show_pinned = true;
} else {
    $show_pinned = false;
    $pinned_result = array();
}

//Page Script
$page_query = 'SELECT aid,user_id,title,board_id,post_date FROM article WHERE board_id = ? AND property = ? ORDER BY aid DESC';
$page_stmt = $conn->stmt_init();
$rows = array();
//Check page result
$page_stmt->prepare($page_query);
$articleProperty = 0;
$page_stmt->bind_param('ii', $board_id, $articleProperty);
$page_stmt->execute();
$page_stmt->bind_result($aid, $user_id, $title, $board_id, $post_date);
$page_result = $page_stmt->get_result();
if ($page_result->num_rows != 0) {
    $show_page = true;
    //Get page info
    while ($page_row = $page_result->fetch_assoc()) {
        $user_query = 'SELECT display_name,username FROM user WHERE uid = ?';
        $user_stmt = $conn->stmt_init();
        //Get user info
        $user_stmt->prepare($user_query);
        $user_stmt->bind_param('i', $page_row['user_id']);
        $user_stmt->execute();
        $user_stmt->bind_result($display_name, $username);
        $user_result = $user_stmt->get_result();
        if ($user_result->num_rows != 0) {
            $show_user = true;
            $countData = DataRead::getInstance();
            $countData->getConnection($conn);
            while ($user_row = $user_result->fetch_assoc()) {
                $lastReply = $countData->getLastReply($page_row['aid']);
                $totalReply = $countData->getReplyByArticle($page_row['aid']);
                $totalReply = $totalReply['count'];
                $article[] = array(
                    'aid' => $page_row['aid'],
                    'user_id' => $page_row['user_id'],
                    'title' => $page_row['title'],
                    'post_date' => getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $page_row['post_date'], 'Y-m-d H:i'),
                    'display_name' => $user_row['display_name'],
                    'username' => $user_row['username'],
                    'show_reply' => ($lastReply !== false) ? true : false,
                    'last' => $lastReply,
                    'reply_page' => countTotalPage($totalReply + 1),
                    'reply_date' => getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $lastReply['reply_date'], 'Y-m-d H:i'),
                    'total_reply' => $totalReply,
                    'total_comment' => $countData->getCommentByArticle($page_row['aid'])
                );
            }
        } else {
            $show_user = false;
        }
    }
} else {
    $show_page = false;
}

if ($show_page === true) {
    $itemsPerPage = 10;
    $currentPage = $current_page;
    $urlPattern = 'board.php?bid='.$board_id.'&amp;page=(:num)';
    $paginator = new Pagination($article, $itemsPerPage, $currentPage, $urlPattern);
    $article_result = $paginator->getResults();
    $total_article = $paginator->getTotalItems() + count($pinned_result);
    $total_pages = $paginator->getNumPages();
    if ($currentPage > $total_pages) {
        header('HTTP/1.0 404 Not Found');
    }
} else {
    $total_article = 0;
    $total_pages = 0;
}

//Load Template
include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));

//Close database connect
$board_stmt->free_result();
$conn->close();
