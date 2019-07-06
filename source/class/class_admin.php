<?php
if (defined('IN_ADMIN') !== true) {
    exit('Access Denied');
}

class Admin
{
    private static $config_id = 1;
    private static $instance = null;
    private $members = array();

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

    public function showData($dataType, $showOrder = 'ASC')
    {
        $data['order'] = $this->detectDataOrder($showOrder);
        $dataInfo = $this->detectDataType($dataType);
        if ($dataInfo['query'] !== false) {
            $data['stmt'] = $this->connectdb->stmt_init();
            try {
                $data['stmt']->prepare($dataInfo['query'].$data['order']);
                if ($dataInfo['type'] === 'article') {
                    $articleProperty = 0;
                    $articlePinned = 3;
                    $data['stmt']->bind_param('ii', $articleProperty, $articlePinned);
                }
                $data['stmt']->execute();
                $data['result'] = $data['stmt']->get_result();
                if ($data['result']->num_rows > 0) {
                    while ($data['row'] = $data['result']->fetch_assoc()) {
                        $this->datas[] = $data['row'];
                    }
                } else {
                    return false;
                }
                $data['stmt']->free_result();
            } catch (mysqli_sql_exception $e) {
                echo '<h1>Service unavailable</h1>'."\n";
                echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
                echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
                exit();
            }
            return $this->datas;
        }
        return false;
    }

    public function searchData($dataType, $keyword, $order)
    {
        $data['order'] = $this->detectDataOrder($order);
        $dataInfo = $this->detectSearchType($dataType);
        if ($dataInfo['query'] !== false) {
            $keyword = trim($keyword);
            if (!empty($keyword)) {
                $data['query'] = $dataInfo['query'].$data['order'];
                $data['stmt'] = $this->connectdb->stmt_init();
                $data['stmt']->prepare($data['query']);
                if ($dataInfo['type'] === 'article') {
                    $articleProperty = 0;
                    $articlePinned = 3;
                    $data['stmt']->bind_param('sssii', $keyword, $keyword, $keyword, $articleProperty, $articlePinned);
                } else {
                    $data['stmt']->bind_param('ss', $keyword, $keyword);
                }
                $data['stmt']->execute();
                $data['result'] = $data['stmt']->get_result();
                $data['row'] = $data['result']->fetch_all(MYSQLI_ASSOC);
                $data['stmt']->free_result();
                return $data['row'];
            } else {
                $dataInfo = $this->detectDataType($dataType);
                $data['query'] = $dataInfo['query'].$data['order'];
                $data['stmt'] = $this->connectdb->stmt_init();
                $data['stmt']->prepare($data['query']);
                if ($dataInfo['type'] === 'article') {
                    $articleProperty = 0;
                    $articlePinned = 3;
                    $data['stmt']->bind_param('ii', $articleProperty, $articlePinned);
                }
                $data['stmt']->execute();
                $data['result'] = $data['stmt']->get_result();
                $data['row'] = $data['result']->fetch_all(MYSQLI_ASSOC);
                $data['stmt']->free_result();
                return $data['row'];
            }
        }
    }

    public function showAdmin($showOrder = 'ASC')
    {
        $admin['order'] = $this->detectDataOrder($showOrder);
        $admin_set = 1;
        $admin['query'] = 'SELECT uid,display_name,username,bio,email,banned,is_admin FROM user WHERE is_admin = ? ORDER BY uid '.$admin['order'];
        $admin['stmt'] = $this->connectdb->stmt_init();
        try {
            $admin['stmt']->prepare($admin['query']);
            $admin['stmt']->bind_param('i', $admin_set);
            $admin['stmt']->execute();
            $admin['stmt']->bind_result($uid, $display_name, $username, $bio, $email, $banned, $is_admin);
            $admin['result'] = $admin['stmt']->get_result();
            while ($admin['row'] = $admin['result']->fetch_assoc()) {
                $this->admins[] = $admin['row'];
            }
            $admin['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
        return $this->admins;
    }

    public function showPinned($showOrder = 'ASC')
    {
        $pinned['order'] = $this->detectDataOrder($showOrder);
        $pinned_set = 3;
        $pinned['query'] = 'SELECT aid,
            user_id,
            user.display_name,
            user.username,
            title,
            article.description,
            board_id,
            board.name,
            article.set_sitemap,
            article.changefreq,
            article.priority,
            article.property,
            article.last_edit,
            post_date FROM article 
            INNER JOIN user ON article.user_id = user.uid 
            INNER JOIN board ON article.board_id = board.bid 
            WHERE property = ? ORDER BY aid ';
        $pinned['stmt'] = $this->connectdb->stmt_init();
        try {
            $pinned['stmt']->prepare($pinned['query']);
            $pinned['stmt']->bind_param('i', $pinned_set);
            $pinned['stmt']->execute();
            $pinned['result'] = $pinned['stmt']->get_result();
            while ($pinned['row'] = $pinned['result']->fetch_assoc()) {
                $this->pinneds[] = $pinned['row'];
            }
            $pinned['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
        return $this->pinneds;
    }

    public function countData($dataType, $showOrder = 'ASC')
    {
        $data['order'] = $this->detectDataOrder($showOrder);
        $dataInfo = $this->detectDataType($dataType);
        $data['stmt'] = $this->connectdb->stmt_init();
        try {
            $data['stmt']->prepare($dataInfo['query'].$data['order']);
            if ($dataInfo['type'] === 'article') {
                $articleProperty = 0;
                $articlePinned = 3;
                $data['stmt']->bind_param('ii', $articleProperty, $articlePinned);
            }
            $data['stmt']->execute();
            $data['result'] = $data['stmt']->get_result();
            while ($data['row'] = $data['result']->fetch_assoc()) {
                $this->datas[] = $data['row'];
            }
            $data['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
        return $this->datas;
    }

    private function detectDataType($type)
    {
        if (isset($type)) {
            switch ($type) {
                case 'member' === $type:
                    $data['type'] = 'member';
                    $data['query'] = 'SELECT uid,display_name,username,bio,email,is_admin,banned FROM user ORDER BY uid ';
                    break;
                case 'article' === $type:
                    $data['type'] = 'article';
                    $data['query'] = 'SELECT aid,
                    user_id,
                    user.display_name,
                    user.username,
                    title,
                    article.description,
                    board_id,
                    board.name,
                    article.set_sitemap,
                    article.changefreq,
                    article.priority,
                    article.property,
                    article.last_edit,
                    post_date FROM article 
                    INNER JOIN user ON article.user_id = user.uid 
                    INNER JOIN board ON article.board_id = board.bid 
                    WHERE (property = ? OR property = ?) ORDER BY aid ';
                    break;
                case 'board' === $type:
                    $data['type'] = 'board';
                    $data['query'] = 'SELECT bid,
                    board.name AS board_name,
                    board.description,
                    category_id,
                    category.name AS category_name,
                    board.set_sitemap,
                    board.changefreq,
                    board.priority,
                    board.last_edit,
                    board.create_date FROM board 
                    INNER JOIN category ON board.category_id = category.cid ORDER BY bid ';
                    break;
                case 'category' === $type:
                    $data['type'] = 'category';
                    $data['query'] = 'SELECT cid,
                    name,
                    description,
                    set_sitemap,
                    changefreq,
                    priority,
                    last_edit,
                    create_date FROM category ORDER BY cid ';
                    break;
                default:
                    $data['type'] = false;
                    $data['query'] = false;
                    break;
            }
        }
        return $data;
    }

    private function detectSearchType($type)
    {
        if (isset($type)) {
            switch ($type) {
                case 'member' === $type:
                    $search['type'] = 'member';
                    $search['query'] = 'SELECT uid,display_name,username,bio,email,is_admin,banned FROM user 
                    WHERE username LIKE CONCAT(\'%\',?,\'%\') 
                    OR email LIKE CONCAT(\'%\',?,\'%\') ORDER BY uid ';
                    break;
                case 'article' === $type:
                    $search['type'] = 'article';
                    $search['query'] = 'SELECT aid,
                    user_id,
                    user.display_name,
                    user.username,
                    title,
                    article.description,
                    board_id,
                    board.name,
                    article.set_sitemap,
                    article.changefreq,
                    article.priority,
                    article.property,
                    article.last_edit,
                    post_date FROM article 
                    INNER JOIN user ON article.user_id = user.uid 
                    INNER JOIN board ON article.board_id = board.bid 
                    WHERE (user.username LIKE CONCAT(\'%\',?,\'%\') 
                    OR article.title LIKE CONCAT(\'%\',?,\'%\') 
                    OR article.description LIKE CONCAT(\'%\',?,\'%\')) 
                    AND (property = ? OR property = ?) ORDER BY aid ';
                    break;
                case 'board' === $type:
                    $search['type'] = 'board';
                    $search['query'] = 'SELECT bid,
                    board.name AS board_name,
                    board.description,
                    category_id,
                    category.name AS category_name,
                    board.set_sitemap,
                    board.changefreq,
                    board.priority,
                    board.last_edit,
                    board.create_date FROM board 
                    INNER JOIN category ON board.category_id = category.cid 
                    WHERE board.name LIKE CONCAT(\'%\',?,\'%\') 
                    OR board.description LIKE CONCAT(\'%\',?,\'%\') ORDER BY bid ';
                    break;
                case 'category' === $type:
                    $search['type'] = 'category';
                    $search['query'] = 'SELECT cid, 
                    name, 
                    description, 
                    set_sitemap, 
                    changefreq, 
                    priority, 
                    last_edit, 
                    create_date FROM category 
                    WHERE name LIKE CONCAT(\'%\',?,\'%\') 
                    OR description LIKE CONCAT(\'%\',?,\'%\') ORDER BY cid ';
                    break;
                default:
                    $search['type'] = false;
                    $search['query'] = false;
                    break;
            }
        }
        return $search;
    }

    private function detectDataOrder($order, $defaultOrder = 'ASC')
    {
        if (isset($order)) {
            switch ($order) {
                case 'DESC' === $order:
                    $detect['order'] = 'DESC';
                    break;
                case 'ASC' === $order:
                    $detect['order'] = 'ASC';
                    break;
                default:
                    $detect['order'] = $defaultOrder;
                    break;
            }
        }
        return $detect['order'];
    }

    public function getOptionList($get_cid = '')
    {
        if (empty($get_cid)) {
            $data['query'] = 'SELECT cid,name,set_sitemap FROM category';
        } else {
            $data['query'] = 'SELECT bid,name,set_sitemap FROM board WHERE category_id = ?';
        }
        $data['stmt'] = $this->connectdb->stmt_init();
        try {
            $data['stmt']->prepare($data['query']);
            if (!empty($get_cid)) {
                $data['stmt']->bind_param('i', $get_cid);
            }
            $data['stmt']->execute();
            $data['result'] = $data['stmt']->get_result();
            if ($data['result']->num_rows > 0) {
                while ($data['row'] = $data['result']->fetch_assoc()) {
                    $option[] = $data['row'];
                }
            } else {
                return false;
            }
            $data['stmt']->free_result();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
        return $option;
    }
}
