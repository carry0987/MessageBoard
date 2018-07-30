<?php
class MetaTag
{
    private $connectdb;
    private $web_config;
    private $seo_main_name;
    private $seo_board_name;
    private $meta = array();

    public function __construct($connect)
    {
        $this->connectdb = $connect;
        $this->web_config = 1;
    }

    public function getMainName()
    {
        $meta['query'] = 'SELECT web_name FROM config WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $this->web_config);
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
        } catch (Exception $e) {
            return 'Sorry';
        }
    }

    public function getArticleTitle($aid)
    {
        $meta['query'] = 'SELECT title,board_id FROM article WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $aid);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($title, $board_id);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['title'].' | '.$this->getBoardTitle($meta['row']['board_id']);
            } else {
                return 'Sorry';
            }
        } catch (Exception $e) {
            return 'Sorry';
        }
    }

    public function getBoardTitle($bid)
    {
        $meta['query'] = 'SELECT board_name FROM board WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $bid);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($board_name);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['board_name'].' | '.$this->getMainName();
            } else {
                return 'Sorry';
            }
        } catch (Exception $e) {
            return 'Sorry';
        }
    }

    public function getArticleDescription($aid)
    {
        $meta['query'] = 'SELECT content FROM article WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $aid);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($content);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['content'];
            } else {
                return 'Sorry';
            }
        } catch (Exception $e) {
            return 'Sorry';
        }
    }

    public function getBoardDescription($bid)
    {
        $meta['query'] = 'SELECT board_description FROM board WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $bid);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($board_description);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['board_description'];
            } else {
                return 'Sorry';
            }
        } catch (Exception $e) {
            return 'Sorry';
        }
    }

    public function getMainDescription()
    {
        $meta['query'] = 'SELECT web_description FROM config WHERE id = ?';
        $meta['stmt'] = $this->connectdb->stmt_init();
        try {
            $meta['stmt']->prepare($meta['query']);
            $meta['stmt']->bind_param('i', $this->web_config);
            $meta['stmt']->execute();
            $meta['stmt']->bind_result($web_description);
            $meta['result'] = $meta['stmt']->get_result();
            if ($meta['result']->num_rows != 0) {
                $meta['row'] = $meta['result']->fetch_assoc();
                return $meta['row']['web_description'];
            } else {
                return 'Sorry';
            }
        } catch (Exception $e) {
            return 'Sorry';
        }
    }
}
