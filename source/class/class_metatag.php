<?php
class MetaTag
{
    private $connectdb;
    private static $web_config = 1;
    private $seo_main_name;
    private $seo_board_name;
    private $meta = array();
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

    public function getMainName()
    {
        $meta['query'] = 'SELECT web_name FROM global_config WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', self::$web_config);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($web_name);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                $this->seo_main_name = $meta['row']['web_name'];
                return $this->seo_main_name;
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getArticleTitle($article_id)
    {
        $meta['query'] = 'SELECT title,board_id FROM article WHERE aid = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $article_id);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($title, $board_id);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['title'].' | '.$this->getBoardTitle($meta['row']['board_id']);
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getBoardTitle($board_id)
    {
        $meta['query'] = 'SELECT name FROM board WHERE bid = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $board_id);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($name);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['name'].' | '.$this->getMainName();
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getCategoryTitle($category_id)
    {
        $meta['query'] = 'SELECT name FROM category WHERE cid = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $category_id);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($name);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['name'].' | '.$this->getMainName();
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getArticleDescription($article_id)
    {
        $meta['query'] = 'SELECT description FROM article WHERE aid = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $article_id);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($description);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['description'];
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getBoardDescription($board_id)
    {
        $meta['query'] = 'SELECT description FROM board WHERE bid = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $board_id);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($description);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['description'];
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getCategoryDescription($category_id)
    {
        $meta['query'] = 'SELECT description FROM category WHERE cid = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $category_id);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($description);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['description'];
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getMainDescription()
    {
        $meta['query'] = 'SELECT web_description FROM global_config WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', self::$web_config);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($web_description);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['web_description'];
            } else {
                return 'Sorry';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }
}
