<?php
class DataRead
{
    private static $config_id = 1;
    private static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function getConnection($connect)
    {
        $this->connectdb = $connect;
    }

    public function getConfig($query)
    {
        $read['query'] = $query['config'];
        $read['stmt'] = $this->connectdb->stmt_init();
        try {
            $read['stmt']->prepare($read['query']);
            $read['stmt']->bind_param('i', self::$config_id);
            $read['stmt']->execute();
            $read['result'] = $read['stmt']->get_result();
            $read['row'] = $read['result']->fetch_assoc();
            return $read['row'];
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getSitemapArray($table, $url_id, $siteURL, $dataID = false)
    {
        $sitemap['query'] = 'SELECT '.$url_id.',changefreq,priority,last_edit FROM '.$table.' WHERE set_sitemap = ?';
        if ($dataID !== false) {
            $sitemap['query'] = 'SELECT '.$url_id.',changefreq,priority,last_edit FROM '.$table.' WHERE set_sitemap = ? AND '.$url_id.' = ?';
        }
        $sitemap['stmt'] = $this->connectdb->stmt_init();
        try {
            $sitemap['stmt']->prepare($sitemap['query']);
            $enable_sitemap = 1;
            if ($dataID !== false) {
                $sitemap['stmt']->bind_param('ii', $enable_sitemap, $dataID);
            } else {
                $sitemap['stmt']->bind_param('i', $enable_sitemap);
            }
            $sitemap['stmt']->execute();
            $sitemap['result'] = $sitemap['stmt']->get_result();
            if ($sitemap['result']->num_rows != 0) {
                $siteURL = rtrim($siteURL, '/\\');
                $siteURL .= '/';
                while ($sitemap['row'] = $sitemap['result']->fetch_assoc()) {
                    $result[] = array(
                        'loc' => $siteURL.$table.'.php?'.$url_id.'='.$sitemap['row'][$url_id],
                        'lastmod' => $sitemap['row']['last_edit'],
                        'changefreq' => $sitemap['row']['changefreq'],
                        'priority' => $sitemap['row']['priority']
                    );
                }
            } else {
                $result = false;
            }
            $sitemap['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            $result = false;
        }
        return $result;
    }

    public function getArticleInfo($article_id = '')
    {
        if (!empty($article_id)) {
            $read['query'] = 'SELECT user_id,title,content,property FROM article WHERE aid = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['stmt']->bind_result($user_id, $title, $content, $property);
                $read['stmt']->fetch();
                $result['user_id'] = $user_id;
                $result['title'] = $title;
                $result['content'] = $content;
                $result['property'] = $property;
                $read['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        }
        return $result;
    }

    public function getPinnedInfo($article_id = '')
    {
        if (!empty($article_id)) {
            $read['query'] = 'SELECT pinned_sort,apply FROM article_pinned WHERE article_id = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['stmt']->bind_result($pinned_sort, $apply);
                $read['stmt']->fetch();
                $result['pinned_sort'] = $pinned_sort;
                $result['apply'] = $apply;
                $read['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        }
        return $result;
    }

    public function getArticleAuthor($article_id = '')
    {
        if (!empty($article_id)) {
            $read['query'] = 'SELECT user_id FROM article WHERE aid = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['stmt']->bind_result($user_id);
                $read['stmt']->fetch();
                $result = (!empty($user_id)) ? $user_id : false;
                $read['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        }
        return $result;
    }

    public function getArticleProperty($article_id = '')
    {
        if (!empty($article_id)) {
            $read['query'] = 'SELECT property FROM article WHERE aid = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['stmt']->bind_result($property);
                $read['stmt']->fetch();
                $result = $property;
                $read['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        }
        return $result;
    }

    public function getArticlePinned($board_id = '')
    {
        $board_id = (int) $board_id;
        $result = false;
        if (!empty($board_id)) {
            $read['query'] = 'SELECT aid,
            title,
            user.uid,
            user.display_name,
            user.username,
            pinned_sort,
            apply FROM article_pinned 
            INNER JOIN article ON article.aid = article_id 
            INNER JOIN user ON user.uid = article.user_id ORDER BY pinned_sort ASC, aid ASC';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                if ($read['result']->num_rows != 0) {
                    while ($read['row'] = $read['result']->fetch_assoc()) {
                        $pinnedArray = unserialize($read['row']['apply']);
                        if (in_array($board_id, $pinnedArray)) {
                            $result[] = $read['row'];
                        }
                    }
                } else {
                    $result = false;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getArticleReply($article_id = '')
    {
        $article_id = (int) $article_id;
        $result = false;
        if (!empty($article_id)) {
            $read['query'] = 'SELECT reply_id,
            user_id,
            content,
            user.display_name,
            user.username,
            user.is_admin,
            user.join_date,
            last_edit,
            reply_date FROM reply 
            INNER JOIN user ON user.uid = reply.user_id WHERE article_id = ? ORDER BY reply_id ASC';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                if ($read['result']->num_rows != 0) {
                    while ($read['row'] = $read['result']->fetch_assoc()) {
                        $result[] = $read['row'];
                    }
                } else {
                    $result = false;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getLastReply($article_id = '')
    {
        $article_id = (int) $article_id;
        $result = false;
        if (!empty($article_id)) {
            $read['query'] = 'SELECT reply_id,
            user_id,
            user.display_name,
            user.username,
            reply_date FROM reply 
            INNER JOIN user ON user.uid = reply.user_id WHERE article_id = ? ORDER BY reply_id DESC LIMIT 1';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                if ($read['result']->num_rows != 0) {
                    $result = $read['result']->fetch_assoc();
                } else {
                    $result = false;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getReplyComment($reply_id = '')
    {
        $reply_id = (int) $reply_id;
        $result = false;
        if (!empty($reply_id)) {
            $read['query'] = 'SELECT comment_id,
            user_id,
            content,
            user.display_name,
            user.username,
            user.is_admin,
            last_edit,
            comment_date FROM comment 
            INNER JOIN user ON user.uid = comment.user_id WHERE reply_id = ? ORDER BY comment_id ASC';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $reply_id);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                if ($read['result']->num_rows != 0) {
                    while ($read['row'] = $read['result']->fetch_assoc()) {
                        $result[] = $read['row'];
                    }
                } else {
                    $result = false;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getArticleByUser($user_id = '')
    {
        $user_id = (int) $user_id;
        if (!empty($user_id)) {
            $read['query'] = 'SELECT aid FROM article WHERE user_id = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $user_id);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                $result = $read['result']->num_rows;
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getReplyByArticle($article_id = '')
    {
        $article_id = (int) $article_id;
        if (!empty($article_id)) {
            $read['query'] = 'SELECT reply_id FROM reply WHERE article_id = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                $result['count'] = $read['result']->num_rows;
                if ($result['count'] > 0) {
                    while ($read['row'] = $read['result']->fetch_assoc()) {
                        $result['replyList'][] = $read['row']['reply_id'];
                    }
                } else {
                    $result['replyList'] = false;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getCommentByArticle($article_id = '')
    {
        $article_id = (int) $article_id;
        if (!empty($article_id)) {
            $read['query'] = 'SELECT comment_id FROM comment WHERE article_id = ?';
            $read['stmt'] = $this->connectdb->stmt_init();
            try {
                $read['stmt']->prepare($read['query']);
                $read['stmt']->bind_param('i', $article_id);
                $read['stmt']->execute();
                $read['result'] = $read['stmt']->get_result();
                $result = $read['result']->num_rows;
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $result = false;
                exit();
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    public function getNotification($user_id)
    {
        if (!empty($user_id) && $user_id !== false) {
            $read['notif_article']['query'] = 'SELECT notif_from, article_id, reply_id, user.display_name, is_read, notif_date 
            FROM notif_article 
            INNER JOIN user ON user.uid = notif_from WHERE notif_to = ? ORDER BY id DESC';
            $read['notif_reply']['query'] = 'SELECT notif_from, article_id, reply_id, comment_id, user.display_name, is_read, notif_date 
            FROM notif_reply 
            INNER JOIN user ON user.uid = notif_from WHERE notif_to = ? ORDER BY id DESC';
            $read['notif_article']['bind'] = 'i';
            $read['notif_reply']['bind'] = 'i';
            $read['notif_article']['param'] = array($user_id);
            $read['notif_reply']['param'] = array($user_id);
            foreach ($read as $key => $value) {
                $result[$key] = $this->getDataList($value);
            }
        } else {
            $result = false;
        }
        return $result;
    }

    private function getDataList($dataArray)
    {
        $result = false;
        $read['query'] = $dataArray['query'];
        $read['stmt'] = $this->connectdb->stmt_init();
        try {
            $read['stmt']->prepare($read['query']);
            $read['stmt']->bind_param($dataArray['bind'], ...$dataArray['param']);
            $read['stmt']->execute();
            $read['result'] = $read['stmt']->get_result();
            if ($read['result']->num_rows != 0) {
                while ($read['row'] = $read['result']->fetch_assoc()) {
                    $result[] = $read['row'];
                }
            } else {
                $result = false;
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function getDataCount($dataArray, $count = false)
    {
        $result = false;
        $read['query'] = $dataArray['query'];
        $read['stmt'] = $this->connectdb->stmt_init();
        try {
            $read['stmt']->prepare($read['query']);
            $read['stmt']->bind_param($dataArray['bind'], ...$dataArray['param']);
            $read['stmt']->execute();
            $read['result'] = $read['stmt']->get_result();
            $result = $read['result']->fetch_assoc();
            $result = $result[$count];
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }
}
