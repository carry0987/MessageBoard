<?php
class SocialAccount
{
    private static $instance = null;
    private static $config_id = 1;

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

    public function socialEnable()
    {
        $github['query'] = 'SELECT enable FROM social_login_config WHERE id = ?';
        $github['stmt'] = $this->connectdb->stmt_init();
        try {
            $github['stmt']->prepare($github['query']);
            $github['stmt']->bind_param('i', self::$config_id);
            $github['stmt']->execute();
            $github['stmt']->bind_result($enable);
            $github['result'] = $github['stmt']->get_result();
            if ($github['result']->num_rows != 0) {
                $github['row'] = $github['result']->fetch_assoc();
                if ($github['row']['enable'] !== 1) {
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

    public function socialLoginType()
    {
        $github['query'] = 'SELECT type FROM social_login_config WHERE id = ?';
        $github['stmt'] = $this->connectdb->stmt_init();
        try {
            $github['stmt']->prepare($github['query']);
            $github['stmt']->bind_param('i', self::$config_id);
            $github['stmt']->execute();
            $github['stmt']->bind_result($type);
            $github['result'] = $github['stmt']->get_result();
            if ($github['result']->num_rows != 0) {
                $github['row'] = $github['result']->fetch_assoc();
                return $github['row']['type'];
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

    public function githubConfig()
    {
        $github['query'] = 'SELECT client_id, client_secret, redirect_url FROM github_login WHERE id = ?';
        $github['stmt'] = $this->connectdb->stmt_init();
        try {
            $github['stmt']->prepare($github['query']);
            $github['stmt']->bind_param('i', self::$config_id);
            $github['stmt']->execute();
            $github['stmt']->bind_result($client_id, $client_secret, $redirect_url);
            $github['result'] = $github['stmt']->get_result();
            if ($github['result']->num_rows != 0) {
                $github['row'] = $github['result']->fetch_assoc();
                foreach ($github['row'] as $key => $value) {
                    $value = trim($value);
                    if (empty($value)) {
                        return false;
                    }
                }
                return $github['row'];
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
