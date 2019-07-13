<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'check', 'data_read', 'data_update', 'data_create', 'data_delete', 'pagination', 'sitemap');
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

if (isset($_GET['aid'])) {
    $check = Check::getInstance();
    $check->getConnection($conn);
    if ($_GET['aid'] == '') {
        header('Location: ./');
        exit();
    } else {
        $article_id = input_filter($_GET['aid']);
        $checkProperty = true;
        if ($check->checkProperty($article_id) === 1) {
            $showPrivate = true;
            if ($check->checkArticleAuthor($article_id, $login['uid']) === true) {
                $checkProperty = true;
            } else {
                $checkProperty = false;
                $show_article = false;
            }
        }
        if ($checkProperty === false) {
            $display = 'view_article_denied';
            header('HTTP/1.0 403 Forbidden');
        }
        //Edit article
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'edit_article') {
                if (!empty($login['username']) && !empty($login['uid'])) {
                    if ($check->checkArticleAuthor($article_id, $login['uid']) === true || $login['admin'] === true) {
                        $edit_post = true;
                        $propertyList = array('public' => 0, 'private' => 1);
                        $current = DataRead::getInstance();
                        $current->getConnection($conn);
                        $articleInfo = $current->getArticleInfo($article_id);
                        $pinnedInfo = $current->getPinnedInfo($article_id);
                        $articleInfo['pinned_sort'] = ($pinnedInfo !== false) ? $pinnedInfo['pinned_sort'] : 1;
                        $display = 'view_edit';
                    } else {
                        $edit_post = false;
                        $display = 'view_edit_denied';
                    }
                } else {
                    $edit_post = false;
                    header('Location: login.php');
                }
            } elseif ($_GET['action'] === 'create_reply') {
                $edit_post = false;
                if (isset($_POST['submitData']) && isset($_POST['reply_content'])) {
                    if ($login['banned'] !== true && $checkProperty === true) {
                        $createReply = DataCreate::getInstance();
                        $createReply->getConnection($conn);
                        if (!empty(input_filter($_POST['reply_content']))) {
                            $replyArray = array('content' => bbcode2html($_POST['reply_content']), 'user_id' => $login['uid']);
                            $replyResult = $createReply->createReply($article_id, $replyArray);
                            $getAuthor = DataRead::getInstance();
                            $getAuthor->getConnection($conn);
                            $notifAuthor = $getAuthor->getArticleAuthor($article_id);
                            $notifAuthor = ($notifAuthor !== false) ? $notifAuthor : false;
                            if (!empty($replyResult) && $notifAuthor !== false && $check->checkArticleAuthor($article_id, $login['uid']) === false) {
                                $notifArray = array(
                                    'user_id' => $notifAuthor,
                                    'notif_from' => $login['uid'],
                                    'reply_id' => $replyResult,
                                    'article_id' => $article_id
                                );
                                $createNotif = $createReply->createArticleNotif($notifArray);
                            }
                        } else {
                            $back_page = 'article.php?aid='.$article_id;
                            $display = 'view_input_error';
                            $replyResult = false;
                        }
                        if (isset($_POST['ajax'])) {
                            $replyResult = ($replyResult !== false) ? $replyResult : 0;
                            echo $replyResult;
                            exit();
                        }
                        if ($replyResult !== false && !isset($_POST['ajax'])) {
                            $reply_process = true;
                            $replyID = 'reply-'.$replyResult;
                            $display = 'view_success';
                        } else {
                            $back['aid'] = $article_id;
                            $display = 'view_reply_error';
                        }
                    } else {
                        if ($checkProperty === true) {
                            $back['aid'] = $article_id;
                            $display = 'view_banned';
                        }
                    }
                }
            } elseif ($_GET['action'] === 'delete') {
                if (isset($_POST['target'])) {
                    $dataDelete = DataDelete::getInstance();
                    $dataDelete->getConnection($conn);
                    switch ($_POST['target']) {
                        case 'article':
                            if (isset($_POST['deleteID'])) {
                                if ($check->checkArticleAuthor($_POST['deleteID'], $login['uid']) === true) {
                                    exit($dataDelete->deleteArticle($_POST['deleteID']));
                                } else {
                                    exit(false);
                                }
                            }
                            break;
                        case 'reply':
                            if (isset($_POST['deleteID'])) {
                                if ($check->checkReplyAuthor($_POST['deleteID'], $login['uid']) === true) {
                                    exit($dataDelete->deleteReply($_POST['deleteID']));
                                } else {
                                    exit(false);
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
         } elseif (isset($_GET['reply'])) {
            $getReplyID = DataRead::getInstance();
            $getReplyID->getConnection($conn);
            $reply_page = $getReplyID->getReplyByArticle($article_id);
            $reply_page = $reply_page['replyList'];
            $reply_pages = 1;
            if (in_array($_GET['reply'], $reply_page)) {
                foreach ($reply_page as $key => $value) {
                    if ($value == $_GET['reply']) {
                        $reply_pages = countTotalPage($key + 2);
                    }
                }
                $readNotif = DataUpdate::getInstance();
                $readNotif->getConnection($conn);
                $readNotif->updateArticleNotif($_GET['reply']);
            }
            header('Location: article.php?aid='.$article_id.'&page='.$reply_pages.'#reply-'.$_GET['reply']);
            exit();
        } else {
            $edit_post = false;
            if ($checkProperty === true) {
                $display = 'view_article';
            }
        }
        //Check edit article
        if ($edit_post === true && isset($_POST['submit'])) {
            $edit_permit = true;
            if (empty($_POST['title']) || empty($_POST['content'])) {
                $edit_permit = false;
                $back_page = 'index.php';
                $display = 'view_input_error';
            }
            if ($edit_permit !== false) {
                if ($check->checkArticleAuthor($article_id, $login['uid']) === true) {
                    $property = (isset($_POST['property'])) ? $_POST['property'] : 0;
                    $property = ($check->checkPinned($article_id) === 3) ? 3 : $_POST['property'];
                    $property = (!isset($_POST['pinned'])) ? $_POST['property'] : 3;
                } else {
                    $checkOwner = DataRead::getInstance();
                    $checkOwner->getConnection($conn);
                    $property = $checkOwner->getArticleProperty($article_id);
                }
                $edit_time = time();
                $edit_info = array(
                    'title' => $_POST['title'],
                    'content' => bbcode2html($_POST['content']),
                    'description' => stripValue(strip_tags(bbcode2html($_POST['content'])), 340),
                    'board_id' => $check->checkBoard($article_id),
                    'set_sitemap' => (isset($_POST['property']) && $_POST['property'] == 0) ? 1 : 0,
                    'property' => $property,
                    'last_edit' => $edit_time
                );
                $edit_article = DataUpdate::getInstance();
                $edit_article->getConnection($conn);
                $edit_article->updateArticle($article_id, $edit_info);
                //Edit Sitemap
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
                            if ($edit_info['set_sitemap'] === 0) {
                                $sitemapArray = array(array('loc' => $base_url.'/article.php?aid=', 'dataID' => (int) $article_id));
                                deleteSitemap($sitemapArray, $sitemap);
                            } else {
                                $sitemapArray = array(
                                    $sitemapConfig->getSitemapArray('article', 'aid', $base_url, $article_id)
                                );
                                generateSitemap($sitemapArray, $sitemap, true);
                            }
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
                //Check pinned
                if (isset($_POST['pinned'])) {
                    $articlePinned['pinned_sort'] = (isset($_POST['pinned_sort']) && !empty($_POST['pinned_sort'])) ? $_POST['pinned_sort'] : 1;
                    $articlePinnedArray = DataRead::getInstance();
                    $articlePinnedArray->getConnection($conn);
                    $pinnedApply = $articlePinnedArray->getPinnedInfo($article_id);
                    if ($pinnedApply !== false) {
                        $pinnedArray = unserialize($pinnedApply['apply']);
                        $getBoard = array($check->checkBoard($article_id));
                        if (is_array($pinnedArray)) {
                            array_push($pinnedArray, $check->checkBoard($article_id));
                            $articlePinned['apply'] = serialize($pinnedArray);
                        } else {
                            $articlePinned['apply'] = serialize($getBoard);
                        }
                    } else {
                        $getBoard = array($check->checkBoard($article_id));
                        $articlePinned['apply'] = serialize($getBoard);
                    }
                    if ($check->checkPinned($article_id) === 3) {
                        $edit_article->updateArticlePinned($article_id, $articlePinned);
                    } else {
                        $addPinned = DataCreate::getInstance();
                        $addPinned->getConnection($conn);
                        $addPinned->createArticlePinned($article_id, $articlePinned);
                    }
                } else {
                    $deletePinned = DataDelete::getInstance();
                    $deletePinned->getConnection($conn);
                    $deletePinned->deleteArticlePinned($article_id);
                }
                $display = 'view_success';
            } else {
                $back_page = 'article.php?aid='.$article_id;
                $display = 'view_input_error';
            }
        }
    }
    $show_edit = ($check->checkArticleAuthor($article_id, $login['uid']) === true || $login['admin'] === true) ? true : false ;
    //Check page value
    if (!empty($_GET['page']) && ctype_digit($_GET['page'])) {
        if ($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
            header('Location: '.$base_url.'/article.php?aid='.$article_id);
        } else {
            $current_page = input_filter($_GET['page']);
        }
    } else {
        $current_page = 1;
    }
} else {
    $article_id = '';
    header('Location: ./');
    exit();
}

//Prepare Article Info
$query = 'SELECT aid,user_id,title,content,board_id,post_date FROM article WHERE aid = ?';
$stmt = $conn->stmt_init();

//Prepare author total article
$author_article_query = 'SELECT aid FROM article WHERE user_id = ?';
$author_article_stmt = $conn->stmt_init();

//Prepare author information
$author_info_query = 'SELECT display_name,username,is_admin,join_date FROM user WHERE uid = ?';
$author_info['stmt'] = $conn->stmt_init();

//Get category, board, article result
if ($stmt->prepare($query) && $checkProperty === true) {
    $stmt->bind_param('i', $article_id);
    $stmt->execute();
    $stmt->bind_result($aid, $user_id, $title, $content, $board_id, $post_date);
    $result = $stmt->get_result();
    if ($result->num_rows != 0) {
        $show_article = true;
        while ($row = $result->fetch_assoc()) {
            $date_format = 'Y-m-d H:i:s';
            $post_date = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $row['post_date'], $date_format);
            $article_result = $row;
            //Get author total article
            if ($author_article_stmt->prepare($author_article_query)) {
                $author_article_stmt->bind_param('i', $row['user_id']);
                $author_article_stmt->execute();
                $author_article_stmt->store_result();
                if ($author_article_stmt->num_rows != 0) {
                    $article_result['article_count'] = $author_article_stmt->num_rows;
                } else {
                    $article_result['article_count'] = 0;
                }
                $author_article_stmt->free_result();
            }
            //Get author informarion
            if ($author_info['stmt']->prepare($author_info_query)) {
                $author_info['stmt']->bind_param('i', $row['user_id']);
                $author_info['stmt']->execute();
                $author_info['stmt']->bind_result($display_name, $username, $is_admin, $join_date);
                $author_info['result'] = $author_info['stmt']->get_result();
                $author_info['row'] = $author_info['result']->fetch_assoc();
                $author_join_date = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $author_info['row']['join_date'], 'Y-m-d');
                $author_info['stmt']->free_result();
            }
            $article_result['is_admin'] = $author_info['row']['is_admin'];
            $article_result['user_id'] = $row['user_id'];
            $article_result['join_date'] = $author_join_date;
            $article_result['display_name'] = $author_info['row']['display_name'];
            $article_result['username'] = $author_info['row']['username'];
            $article_result['reply_id'] = 0;
            $article_result['post_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $row['post_date']);
            $article_title = $row['title'];
            $article_author_id = $row['user_id'];
            $article_content = $row['content'];
            //Get board info
            $board_query = 'SELECT bid,name,category_id FROM board WHERE bid = ?';
            $board_stmt = $conn->stmt_init();
            if ($board_stmt->prepare($board_query)) {
                $board_stmt->bind_param('i', $row['board_id']);
                $board_stmt->execute();
                $board_stmt->bind_result($bid, $name, $category_id);
                $board_result = $board_stmt->get_result();
                while ($board_row = $board_result->fetch_assoc()) {
                    $board_link = $board_row['bid'];
                    $board_from = $board_row['name'];
                    $category_query = 'SELECT name FROM category WHERE cid = ?';
                    $category_stmt = $conn->stmt_init();
                    if ($category_stmt->prepare($category_query)) {
                        $category_stmt->bind_param('i', $board_row['category_id']);
                        $category_stmt->execute();
                        $category_stmt->bind_result($name);
                        $category_result = $category_stmt->get_result();
                        while ($category_row = $category_result->fetch_assoc()) {
                            $category_link = $board_row['category_id'];
                            $category_from = $category_row['name'];
                        }
                    }
                }
            }
        }
        //Breadcrumb
        $article_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //Get Reply
        $replyData = DataRead::getInstance();
        $replyData->getConnection($conn);
        $getReply = $replyData->getArticleReply($article_id);
        if ($getReply !== false) {
            foreach ($getReply as $key => $value) {
                $getReply[$key]['reply_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['reply_date'], 'Y-m-d H:i');
                $getReply[$key]['join_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['join_date'], 'Y-m-d');
                $getReply[$key]['article_count'] = $replyData->getArticleByUser($value['user_id']);
            }
        }
    } else {
        $show_article = false;
        header('HTTP/1.0 404 Not Found');
    }
}

if ($show_article === true) {
    $itemsPerPage = 10;
    $currentPage = $current_page;
    $urlPattern = 'article.php?aid='.$article_id.'&amp;page=(:num)';
    if ($getReply !== false) {
        array_unshift($getReply, $article_result);
        $paginator = new Pagination($getReply, $itemsPerPage, $currentPage, $urlPattern);
        $reply_result = $paginator->getResults();
        $total_reply = $paginator->getTotalItems();
        $total_pages = $paginator->getNumPages();
        $checkPage = countLastPageResult($getReply, $total_pages, $itemsPerPage);
        $checkPage = ($checkPage !== false) ? $checkPage : 0;
        if ($currentPage > $total_pages && $total_pages !== 0) {
            header('Location: article.php?aid='.$article_id);
        }
    } else {
        $checkPage = 0;
        $reply_result[] = $article_result;
        $total_reply = 0;
        $total_pages = 0;
    }
} else {
    $total_reply = 0;
    $total_pages = 0;
}

//Load Template
include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));
