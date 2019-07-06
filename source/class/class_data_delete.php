<?php
class DataDelete
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

    public function deleteUser($user_id, $login_uid)
    {
        if ($user_id != 1 && $user_id != $login_uid) {
            $delete['query'] = 'DELETE FROM user WHERE uid = ?';
            $delete['stmt'] = $this->connectdb->stmt_init();
            try {
                $delete['stmt']->prepare($delete['query']);
                $delete['stmt']->bind_param('i', $user_id);
                $delete['stmt']->execute();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
        }
    }

    public function deleteArticle($article_id)
    {
        $delete['query'] = 'DELETE FROM article WHERE aid = ?';
        $delete['stmt'] = $this->connectdb->stmt_init();
        try {
            $delete['stmt']->prepare($delete['query']);
            $delete['stmt']->bind_param('i', $article_id);
            $delete['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function deleteBoard($board_id)
    {
        $delete['query'] = 'DELETE FROM board WHERE bid = ?';
        $delete['stmt'] = $this->connectdb->stmt_init();
        try {
            $delete['stmt']->prepare($delete['query']);
            $delete['stmt']->bind_param('i', $board_id);
            $delete['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function deleteCategory($category_id)
    {
        $delete['query'] = 'DELETE FROM category WHERE cid = ?';
        $delete['stmt'] = $this->connectdb->stmt_init();
        try {
            $delete['stmt']->prepare($delete['query']);
            $delete['stmt']->bind_param('i', $category_id);
            $delete['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function deleteArticlePinned($article_id)
    {
        $delete['query'] = 'DELETE FROM article_pinned WHERE article_id = ?';
        $delete['stmt'] = $this->connectdb->stmt_init();
        try {
            $delete['stmt']->prepare($delete['query']);
            $delete['stmt']->bind_param('i', $article_id);
            $delete['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function deleteReply($reply_id)
    {
        $delete['query'] = 'DELETE FROM reply WHERE reply_id = ?';
        $delete['stmt'] = $this->connectdb->stmt_init();
        try {
            $delete['stmt']->prepare($delete['query']);
            $delete['stmt']->bind_param('i', $reply_id);
            $delete['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function deleteComment($comment_id)
    {
        $delete['query'] = 'DELETE FROM comment WHERE comment_id = ?';
        $delete['stmt'] = $this->connectdb->stmt_init();
        try {
            $delete['stmt']->prepare($delete['query']);
            $delete['stmt']->bind_param('i', $comment_id);
            $delete['stmt']->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }
}
