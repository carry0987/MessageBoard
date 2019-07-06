<?php
class DataUpdate
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

    public function updateLastlogin($value, $user_id)
    {
        $update['query'] = 'UPDATE user SET last_login = ? WHERE uid = ?';
        $update['stmt'] = $this->connectdb->stmt_init();
        try {
            $update['stmt']->prepare($update['query']);
            $update['stmt']->bind_param('ii', 
                $value, 
                $user_id
            );
            $update['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function updateConfig($query)
    {
        $update['query'] = $query['config'];
        $update['stmt'] = $this->connectdb->stmt_init();
        try {
            $update['stmt']->prepare($update['query']);
            $update['stmt']->bind_param($query['prepare'], ...$query['bind']);
            $update['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function updatePassword($get_password, $get_email)
    {
        $update['query'] = 'UPDATE user SET password = ? WHERE email = ?';
        $update['stmt'] = $this->connectdb->stmt_init();
        try {
            $update['stmt']->prepare($update['query']);
            $update['stmt']->bind_param('ss', 
                $get_password, 
                $get_email
            );
            if ($update['stmt']->execute() === false) {
                return false;
            } else {
                return true;
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function updateUser($user_id, $query)
    {
        if ($user_id != 1) {
            $update['query'] = 'UPDATE user SET display_name = ?,username = ?,bio = ?,is_admin = ?,banned = ? WHERE uid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('sssiii', 
                    $query['display_name'], 
                    $query['username'],
                    $query['bio'],
                    $query['is_admin'],
                    $query['banned'],
                    $user_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateMultiUser($user_id, $query)
    {
        if ($user_id != 1) {
            $update['query'] = 'UPDATE user SET is_admin = ?,banned = ? WHERE uid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('iii', 
                    $query['is_admin'],
                    $query['banned'],
                    $user_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateArticle($article_id, $query)
    {
        if (!empty($article_id)) {
            $last_edit = time();
            $update['query'] = 'UPDATE article SET title = ?, content = ?, description = ?, board_id = ?, set_sitemap = ?, property = ?, last_edit = ? WHERE aid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('sssiiiii', 
                    $query['title'],
                    $query['content'],
                    $query['description'],
                    $query['board_id'],
                    $query['set_sitemap'],
                    $query['property'],
                    $last_edit,
                    $article_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateArticleBoard($article_id, $query)
    {
        if (!empty($article_id)) {
            $last_edit = time();
            $update['query'] = 'UPDATE article SET board_id = ?, last_edit = ?, set_sitemap = ? WHERE aid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('iiii', 
                    $query['board_id'],
                    $last_edit,
                    $query['set_sitemap'],
                    $article_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateArticlePinned($article_id, $query)
    {
        if (!empty($article_id)) {
            $property = 3;
            $update['query'] = 'UPDATE article_pinned AS t1 JOIN article AS t2 ON t1.article_id = t2.aid 
            SET t1.pinned_sort = ?, t1.apply = ?, t2.property = ? WHERE t1.article_id = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('isii', 
                    $query['pinned_sort'],
                    $query['apply'],
                    $property,
                    $article_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updatePinnedSort($article_id, $query)
    {
        if (!empty($article_id)) {
            $update['query'] = 'UPDATE article_pinned SET pinned_sort = ? WHERE article_id = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('ii', 
                    $query['pinned_sort'],
                    $article_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateBoard($board_id, $query)
    {
        if (!empty($board_id)) {
            $last_edit = time();
            $update['query'] = 'UPDATE board AS b LEFT JOIN article AS a ON b.bid = a.board_id 
            SET name = ?, b.description = ?, category_id = ?, b.set_sitemap = ?, a.set_sitemap = ?, b.last_edit = ? WHERE bid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('ssiiiii', 
                    $query['board_name'],
                    $query['board_description'],
                    $query['category_id'],
                    $query['set_sitemap'],
                    $query['set_sitemap'],
                    $last_edit,
                    $board_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateBoardCategory($board_id, $query)
    {
        if (!empty($board_id)) {
            $update['query'] = 'UPDATE board AS b LEFT JOIN article AS a ON b.bid = a.board_id 
            SET category_id = ?, b.set_sitemap = ?, a.set_sitemap = ? WHERE bid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('iiii', 
                    $query['category_id'],
                    $query['set_sitemap'],
                    $query['set_sitemap'],
                    $board_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateCategory($category_id, $query)
    {
        if (!empty($category_id)) {
            $last_edit = time();
            $update['query'] = 'UPDATE category AS c 
            LEFT JOIN board AS b ON c.cid = b.category_id 
            LEFT JOIN article AS a ON b.bid = a.board_id 
            SET c.name = ?, c.description = ?, c.set_sitemap = ?, b.set_sitemap = ?, a.set_sitemap = ?, c.last_edit = ? WHERE cid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('ssiiiii', 
                    $query['category_name'],
                    $query['category_description'],
                    $query['set_sitemap'],
                    $query['set_sitemap'],
                    $query['set_sitemap'],
                    $last_edit,
                    $category_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateCategorySitemap($category_id, $query)
    {
        if (!empty($category_id)) {
            $update['query'] = 'UPDATE category AS c 
            LEFT JOIN board AS b ON c.cid = b.category_id 
            LEFT JOIN article AS a ON b.bid = a.board_id 
            SET c.set_sitemap = ?, b.set_sitemap = ?, a.set_sitemap = ? WHERE cid = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('iiii', 
                    $query['set_sitemap'],
                    $query['set_sitemap'],
                    $query['set_sitemap'],
                    $category_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateReply($reply_id, $query)
    {
        if (!empty($reply_id)) {
            $last_edit = time();
            $update['query'] = 'UPDATE reply SET content = ?, last_edit = ? WHERE reply_id = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('sii', 
                    $query['content'],
                    $last_edit,
                    $reply_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateComment($comment_id, $query)
    {
        if (!empty($comment_id)) {
            $last_edit = time();
            $update['query'] = 'UPDATE comment SET content = ?, last_edit = ? WHERE comment_id = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('sii', 
                    $query['content'],
                    $last_edit,
                    $comment_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function updateArticleNotif($notif_id)
    {
        $notif_id = (int) $notif_id;
        if (!empty($notif_id)) {
            $read_date = time();
            $read = 1;
            $update['query'] = 'UPDATE notif_article SET is_read = ?, read_date = ? WHERE reply_id = ?';
            $update['stmt'] = $this->connectdb->stmt_init();
            try {
                $update['stmt']->prepare($update['query']);
                $update['stmt']->bind_param('iii', 
                    $read,
                    $read_date,
                    $notif_id
                );
                if ($update['stmt']->execute() === false) {
                    return false;
                } else {
                    return true;
                }
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }
}
