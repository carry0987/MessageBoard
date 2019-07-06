<?php
class EmailConfig
{
    private $connectdb;
    private static $config = 1;
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

    public function checkEmailEnable()
    {
        $email['query'] = 'SELECT enable FROM email_config WHERE id = ?';
        $email['stmt'] = $this->connectdb->stmt_init();
        try {
            $email['stmt']->prepare($email['query']);
            $email['stmt']->bind_param('i', self::$config);
            $email['stmt']->execute();
            $email['stmt']->bind_result($enable);
            $email['result'] = $email['stmt']->get_result();
            if ($email['result']->num_rows != 0) {
                $email['row'] = $email['result']->fetch_assoc();
                if ($email['row']['enable'] !== 1) {
                    return false;
                } else {
                    return true;
                }
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

    public function checkEmailType()
    {
        $email['query'] = 'SELECT type FROM email_config WHERE id = ?';
        $email['stmt'] = $this->connectdb->stmt_init();
        try {
            $email['stmt']->prepare($email['query']);
            $email['stmt']->bind_param('i', self::$config);
            $email['stmt']->execute();
            $email['stmt']->bind_result($type);
            $email['result'] = $email['stmt']->get_result();
            if ($email['result']->num_rows != 0) {
                $email['row'] = $email['result']->fetch_assoc();
                return $email['row']['type'];
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

    public function localhostEmailConfig()
    {
        $email['query'] = 'SELECT charset,send_from,send_name FROM email_localhost WHERE id = ?';
        $email['stmt'] = $this->connectdb->stmt_init();
        try {
            $email['stmt']->prepare($email['query']);
            $email['stmt']->bind_param('i', self::$config);
            $email['stmt']->execute();
            $email['stmt']->bind_result($charset, $send_from, $send_name);
            $email['result'] = $email['stmt']->get_result();
            if ($email['result']->num_rows != 0) {
                $email['row'] = $email['result']->fetch_assoc();
                return $email['row'];
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

    public function smtpEmailConfig()
    {
        $email['query'] = 'SELECT charset, smtp_host, smtp_user, smtp_pw, send_from, send_name FROM email_smtp WHERE id = ?';
        $email['stmt'] = $this->connectdb->stmt_init();
        try {
            $email['stmt']->prepare($email['query']);
            $email['stmt']->bind_param('i', self::$config);
            $email['stmt']->execute();
            $email['stmt']->bind_result($charset,$smtp_host,$smtp_user,$smtp_pw,$send_from,$send_name);
            $email['result'] = $email['stmt']->get_result();
            if ($email['result']->num_rows != 0) {
                $email['row'] = $email['result']->fetch_assoc();
                return $email['row'];
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
