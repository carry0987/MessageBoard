<?php
class DataCreate
{
    private $connectdb;
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

    public function createUser($user)
    {
        $create['query'] = 'INSERT INTO user (display_name, username, password, bio, email, language, is_admin, online_status, last_login, join_date) VALUES (?,?,?,?,?,?,?,?,?,?)';
        $create['stmt'] = $this->connectdb->stmt_init();
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('ssssssisii', 
                $user['display_name'], 
                $user['username'], 
                $user['password'], 
                $user['bio'],
                $user['email'], 
                $user['language'], 
                $user['is_admin'], 
                $user['online_status'], 
                $user['last_login'], 
                $user['join_date']
            );
            $create['stmt']->execute();
            $result = true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function createArticle($article)
    {
        $create['query'] = 'INSERT INTO article (user_id, title, content, description, board_id, set_sitemap, property, last_edit, post_date) VALUES (?,?,?,?,?,?,?,?,?)';
        $create['stmt'] = $this->connectdb->stmt_init();
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('isssiiiii', 
                $article['user_id'], 
                $article['title'], 
                $article['content'], 
                $article['article_description'], 
                $article['board_id'], 
                $article['set_sitemap'], 
                $article['property'],
                $article['last_edit'],
                $article['post_date']
            );
            $create['stmt']->execute();
            $getAutoIncrement = $create['stmt']->insert_id;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $getAutoIncrement = false;
            exit();
        }
        return $getAutoIncrement;
    }

    public function createBoard($board)
    {
        $create['query'] = 'INSERT INTO board (name, description, category_id, set_sitemap, last_edit, create_date) VALUES (?,?,?,?,?,?)';
        $create['stmt'] = $this->connectdb->stmt_init();
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('ssiiii', 
                $board['board_name'], 
                $board['board_description'], 
                $board['category_id'], 
                $board['set_sitemap'], 
                $board['last_edit'],
                $board['create_date']
            );
            $create['stmt']->execute();
            $result = true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function createCategory($category)
    {
        $create['query'] = 'INSERT INTO category (name, description, set_sitemap, last_edit, create_date) VALUES (?,?,?,?,?)';
        $create['stmt'] = $this->connectdb->stmt_init();
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('ssiii', 
                $category['category_name'], 
                $category['category_description'], 
                $category['set_sitemap'], 
                $category['last_edit'],
                $category['create_date']
            );
            $create['stmt']->execute();
            $result = true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function createArticlePinned($article_id, $query)
    {
        $create['query'] = 'INSERT INTO article_pinned (article_id, pinned_sort, apply) VALUES (?,?,?)';
        $update['query'] = 'UPDATE article SET property = ? WHERE aid = ?';
        $create['stmt'] = $this->connectdb->stmt_init();
        $update['stmt'] = $this->connectdb->stmt_init();
        $property = 3;
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('iis', 
                $article_id, 
                $query['pinned_sort'], 
                $query['apply']
            );
            $create['stmt']->execute();
            $update['stmt']->prepare($update['query']);
            $update['stmt']->bind_param('ii', 
                $property,
                $article_id
            );
            $update['stmt']->execute();
            $result = true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function createAvatarData($user_id, $query)
    {
        $create['query'] = 'INSERT INTO attach_avatar (user_id, origin_name, file_name, upload_date) VALUES (?,?,?,?)';
        $create['stmt'] = $this->connectdb->stmt_init();
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('issi', 
                $user_id, 
                $query['origin_name'], 
                $query['file_name'], 
                $query['upload_date']
            );
            $create['stmt']->execute();
            $result = true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function createImageData($article_id, $query)
    {
        $create['query'] = 'INSERT INTO attach_image (article_id, origin_name, file_name, upload_date) VALUES (?,?,?,?)';
        $create['stmt'] = $this->connectdb->stmt_init();
        try {
            $create['stmt']->prepare($create['query']);
            $create['stmt']->bind_param('issi', 
                $article_id, 
                $query['origin_name'], 
                $query['file_name'], 
                $query['upload_date']
            );
            $create['stmt']->execute();
            $result = true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $result = false;
            exit();
        }
        return $result;
    }

    public function createReply($article_id, $query)
    {
        if (!empty($query['user_id']) && $query['user_id'] !== false) {
            $result = false;
            $create['query'] = 'INSERT INTO reply (user_id, content, article_id, last_edit, reply_date) VALUES (?,?,?,?,?)';
            $create['stmt'] = $this->connectdb->stmt_init();
            $query['last_edit'] = time();
            $query['reply_date'] = time();
            try {
                $create['stmt']->prepare($create['query']);
                $create['stmt']->bind_param('isiii', 
                    $query['user_id'], 
                    $query['content'], 
                    $article_id, 
                    $query['last_edit'], 
                    $query['reply_date']
                );
                $create['stmt']->execute();
                $result = $create['stmt']->insert_id;
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

    public function createComment($reply_id, $query)
    {
        if (!empty($query['user_id']) && $query['user_id'] !== false) {
            $result = false;
            $create['query'] = 'INSERT INTO comment (user_id, content, reply_id, last_edit, comment_date) VALUES (?,?,?,?,?)';
            $create['stmt'] = $this->connectdb->stmt_init();
            $query['last_edit'] = time();
            $query['reply_date'] = time();
            try {
                $create['stmt']->prepare($create['query']);
                $create['stmt']->bind_param('isiii', 
                    $query['user_id'], 
                    $query['content'], 
                    $reply_id, 
                    $query['last_edit'], 
                    $query['comment_date']
                );
                $create['stmt']->execute();
                $result = $create['stmt']->insert_id;
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

    public function createArticleNotif($query)
    {
        $result = false;
        if (!empty($query['user_id']) && $query['user_id'] !== false) {
            $create['query'] = 'INSERT INTO notif_article (notif_to, notif_from, article_id, reply_id, notif_date, read_date) VALUES (?,?,?,?,?,?)';
            $create['stmt'] = $this->connectdb->stmt_init();
            $time = time();
            $read_date = 0;
            try {
                $create['stmt']->prepare($create['query']);
                $create['stmt']->bind_param('iiiiii', 
                    $query['user_id'], 
                    $query['notif_from'], 
                    $query['article_id'], 
                    $query['reply_id'], 
                    $time, 
                    $read_date
                );
                $create['stmt']->execute();
                $result = true;
            } catch (mysqli_sql_exception $e) {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function createReplyNotif($query)
    {
        $result = false;
        if (!empty($query['user_id']) && $query['user_id'] !== false) {
            $create['query'] = 'INSERT INTO notif_reply (notif_to, notif_from, article_id, reply_id, notif_date, read_date) VALUES (?,?,?,?,?,?)';
            $create['stmt'] = $this->connectdb->stmt_init();
            $time = time();
            $read_date = 0;
            try {
                $create['stmt']->prepare($create['query']);
                $create['stmt']->bind_param('iiiiii', 
                    $query['notif_to'], 
                    $query['notif_from'], 
                    $query['article_id'], 
                    $query['reply_id'], 
                    $time, 
                    $read_date
                );
                $create['stmt']->execute();
                $result = true;
            } catch (mysqli_sql_exception $e) {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }
}
