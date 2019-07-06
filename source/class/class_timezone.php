<?php
class Timezone
{
    private $connectdb;
    private static $web_config = 1;
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

    public function getWebTimezone()
    {
        $timezone['query'] = 'SELECT web_timezone FROM global_config WHERE id = ?';
        $timezone['stmt'] = $this->connectdb->stmt_init();
        try {
            $timezone['stmt']->prepare($timezone['query']);
            $timezone['stmt']->bind_param('i', self::$web_config);
            $timezone['stmt']->execute();
            $timezone['stmt']->bind_result($web_timezone);
            $timezone['result'] = $timezone['stmt']->get_result();
            if ($timezone['result']->num_rows != 0) {
                $timezone['row'] = $timezone['result']->fetch_assoc();
                return $timezone['row']['web_timezone'];
            } else {
                return 'Europe/London';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function getCustomTimezone($uid)
    {
        $timezone['query'] = 'SELECT timezone FROM user_timezone WHERE user_id = ?';
        $timezone['stmt'] = $this->connectdb->stmt_init();
        try {
            $timezone['stmt']->prepare($timezone['query']);
            $timezone['stmt']->bind_param('i', $uid);
            $timezone['stmt']->execute();
            $timezone['stmt']->bind_result($timezone);
            $timezone['result'] = $timezone['stmt']->get_result();
            if ($timezone['result']->num_rows != 0) {
                $timezone['row'] = $timezone['result']->fetch_assoc();
                return $timezone['row']['timezone'];
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }
}
