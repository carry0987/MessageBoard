<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'data_read');
$load->loadFunction('filter', 'core');

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

//Breadcrumb
$index_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Check notification
$getNotif = DataRead::getInstance();
$getNotif->getConnection($conn);
$notifList = $getNotif->getNotification($login['uid']);
if ($notifList !== false && isset($_GET['get_notif']) && checkReferer() === true) {
    foreach ($notifList as $key => $value) {
        if ($notifList[$key] !== false) {
            $notif_result[$key] = $value;
            $countNotif = countArray($value, 99, '99+', array('is_read' => 1));
            array_push($notif_result, $notif_result[$key]);
            unset($notif_result[$key]);
            usort($notif_result[0], function($a, $b) {
                return $b['notif_date'] <=> $a['notif_date'];
            });
            $notif_result[0] = checkNotifLink(
                $notif_result[0],
                array('article_id', 'reply_id'),
                array('article.php?aid=(:article_id:)&reply=(:reply_id:)'),
                $SYSTEM['system_timezone'],
                $SYSTEM['user_timezone']
            );
        }
    }
    $notif_result['notif_count'] = (isset($countNotif)) ? $countNotif : 0;
    include($template->loadTemplate('ajax_notif_list.html'));
    exit();
}

//Check new notification
if ($login['uid'] !== false) {
    $getTotal[] = array();
    $unRead = 0;
    $getTotal['article']['query'] = 'SELECT COUNT(id) AS notif_count FROM notif_article WHERE notif_to = ? AND is_read = ?';
    $getTotal['article']['bind'] = 'ii';
    $getTotal['article']['param'] = array($login['uid'], $unRead);
    $getTotal['reply']['query'] = 'SELECT COUNT(id) AS notif_count FROM notif_reply WHERE notif_to = ? AND is_read = ?';
    $getTotal['reply']['bind'] = 'ii';
    $getTotal['reply']['param'] = array($login['uid'], $unRead);
    $total_notif = $getNotif->getDataCount($getTotal['article'], 'notif_count') + $getNotif->getDataCount($getTotal['reply'], 'notif_count');
    if (isset($_GET['get_notif_total']) && checkReferer() === true) {
        exit(json_encode($total_notif));
    }
}

//Category list
$category_query = 'SELECT cid,name,description FROM category ORDER BY cid ASC';
$category_stmt = $conn->stmt_init();
$categorys = array();

if ($category_stmt->prepare($category_query)) {
    $category_stmt->execute();
    $category_stmt->bind_result($cid, $name, $description);
    $category_result = $category_stmt->get_result();
    if ($category_result->num_rows != 0) {
        while ($category_row = $category_result->fetch_assoc()) {
            $category_row['boards'] = array();
            $board_query = 'SELECT bid,name FROM board WHERE category_id = ? ORDER BY bid ASC';
            $board_stmt = $conn->stmt_init();
            if ($board_stmt->prepare($board_query)) {
                $board_stmt->bind_param('i', $category_row['cid']);
                $board_stmt->execute();
                $board_stmt->bind_result($bid, $name);
                $board_result = $board_stmt->get_result();
                if ($board_result->num_rows != 0) {
                    $show_board_list = true;
                    $articleProperty = 0;
                    $articlePinned = 3;
                    while ($board_row = $board_result->fetch_assoc()) {
                        $total_article_query = 'SELECT aid,user_id,title,post_date FROM article WHERE board_id = ? AND (property = ? OR property = ?)';
                        $total_article_stmt = $conn->stmt_init();
                        if ($total_article_stmt->prepare($total_article_query)) {
                            $total_article_stmt->bind_param('iii', $board_row['bid'], $articleProperty, $articlePinned);
                            $total_article_stmt->execute();
                            $total_article_stmt->bind_result($aid, $user_id, $title ,$post_date);
                            $total_article_result = $total_article_stmt->get_result();
                            if ($total_article_result->num_rows != 0) {
                                $board_row['articles'] = array();
                                while ($total_article_row = $total_article_result->fetch_assoc()) {
                                    $board_row['articles'][] = $total_article_row;
                                }
                            }
                            //Get latest article
                            $latest_query = 'SELECT aid,user_id,title,post_date FROM article WHERE board_id = ? AND (property = ? OR property = ?) ORDER BY aid DESC LIMIT 1';
                            $latest_stmt = $conn->stmt_init();
                            if ($latest_stmt->prepare($latest_query)) {
                                $latest_stmt->bind_param('iii', $board_row['bid'], $articleProperty, $articlePinned);
                                $latest_stmt->execute();
                                $latest_stmt->bind_result($aid, $user_id, $title ,$post_date);
                                $latest_result = $latest_stmt->get_result();
                                if ($latest_result->num_rows != 0) {
                                    $board_row['latest'] = array();
                                    while ($latest_row = $latest_result->fetch_assoc()) {
                                        $board_row['latest'] = $latest_row;
                                        $latest_date = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $board_row['latest']['post_date'], 'Y-m-d H:m');
                                        $latest_username_query = 'SELECT display_name,username FROM user WHERE uid = ?';
                                        $latest_username_stmt = $conn->stmt_init();
                                        if ($latest_username_stmt->prepare($latest_username_query)) {
                                            $latest_username_stmt->bind_param('i', $board_row['latest']['user_id']);
                                            $latest_username_stmt->execute();
                                            $latest_username_stmt->bind_result($display_name, $username);
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
