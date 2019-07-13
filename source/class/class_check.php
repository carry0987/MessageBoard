<?php
class Check
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

    public function checkUsername($get_username)
    {
        $username_query = 'SELECT username FROM user WHERE username = ?';
        $username_stmt = $this->connectdb->stmt_init();
        try {
            $username_stmt->prepare($username_query);
            $username_stmt->bind_param('s', $get_username);
            $username_stmt->execute();
            $username_stmt->bind_result($username);
            $username_result = $username_stmt->get_result();
            if ($username_result->num_rows != 0) {
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

    public function checkEmail($get_email)
    {
        $email_query = 'SELECT email FROM user WHERE email = ?';
        $email_stmt = $this->connectdb->stmt_init();
        try {
            $email_stmt->prepare($email_query);
            $email_stmt->bind_param('s', $get_email);
            $email_stmt->execute();
            $email_stmt->bind_result($email);
            $email_result = $email_stmt->get_result();
            if ($email_result->num_rows != 0) {
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

    public function checkArticleAuthor($article_id, $get_uid = '')
    {
        $check['query'] = 'SELECT user_id FROM article WHERE aid = ?';
        $check['stmt'] = $this->connectdb->stmt_init();
        try {
            $check['stmt']->prepare($check['query']);
            $check['stmt']->bind_param('i', $article_id);
            $check['stmt']->execute();
            $check['stmt']->bind_result($user_id);
            $check['stmt']->fetch();
            if ($user_id === $get_uid) {
                $checkResult = true;
            } else {
                $checkResult = false;
            }
            $check['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $checkResult = false;
            exit();
        }
        return $checkResult;
    }

    public function checkReplyAuthor($reply_id, $get_uid = '')
    {
        $check['query'] = 'SELECT user_id FROM reply WHERE reply_id = ?';
        $check['stmt'] = $this->connectdb->stmt_init();
        try {
            $check['stmt']->prepare($check['query']);
            $check['stmt']->bind_param('i', $reply_id);
            $check['stmt']->execute();
            $check['stmt']->bind_result($user_id);
            $check['stmt']->fetch();
            if ($user_id === $get_uid) {
                $checkResult = true;
            } else {
                $checkResult = false;
            }
            $check['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            $checkResult = false;
            exit();
        }
        return $checkResult;
    }

    public function checkBoard($article_id = '')
    {
        if (!empty($article_id)) {
            $check['query'] = 'SELECT board_id FROM article WHERE aid = ?';
            $check['stmt'] = $this->connectdb->stmt_init();
            try {
                $check['stmt']->prepare($check['query']);
                $check['stmt']->bind_param('i', $article_id);
                $check['stmt']->execute();
                $check['stmt']->bind_result($board_id);
                $check['stmt']->fetch();
                $checkResult = $board_id;
                $check['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $checkResult = false;
                exit();
            }
        } else {
            $checkResult = false;
        }
        return $checkResult;
    }

    public function checkProperty($article_id = '')
    {
        if (!empty($article_id)) {
            $article_id = (int) $article_id;
            $check['query'] = 'SELECT property FROM article WHERE aid = ?';
            $check['stmt'] = $this->connectdb->stmt_init();
            try {
                $check['stmt']->prepare($check['query']);
                $check['stmt']->bind_param('i', $article_id);
                $check['stmt']->execute();
                $check['stmt']->bind_result($property);
                $check['stmt']->fetch();
                $checkResult = ($property !== null) ? $property : false;
                $check['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $checkResult = false;
                exit();
            }
        } else {
            $checkResult = false;
        }
        return $checkResult;
    }

    public function checkPinned($article_id = '')
    {
        if (!empty($article_id)) {
            $check['query'] = 'SELECT property FROM article INNER JOIN article_pinned AS ap ON article.aid = ap.article_id WHERE aid = ?';
            $check['stmt'] = $this->connectdb->stmt_init();
            try {
                $check['stmt']->prepare($check['query']);
                $check['stmt']->bind_param('i', $article_id);
                $check['stmt']->execute();
                $check['stmt']->bind_result($property);
                $check['stmt']->fetch();
                $checkResult = $property;
                $check['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                $checkResult = false;
                exit();
            }
        } else {
            $checkResult = false;
        }
        return $checkResult;
    }
}
