<?php
class UploadConfig
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

    public function checkUploadEnable()
    {
        $upload['query'] = 'SELECT enable FROM upload_config WHERE id = ?';
        $upload['stmt'] = $this->connectdb->stmt_init();
        try {
            $upload['stmt']->prepare($upload['query']);
            $upload['stmt']->bind_param('i', self::$config);
            $upload['stmt']->execute();
            $upload['stmt']->bind_result($enable);
            $upload['result'] = $upload['stmt']->get_result();
            if ($upload['result']->num_rows != 0) {
                $upload['row'] = $upload['result']->fetch_assoc();
                if ($upload['row']['enable'] !== 1) {
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

    public function checkUploadType()
    {
        $upload['query'] = 'SELECT type FROM upload_config WHERE id = ?';
        $upload['stmt'] = $this->connectdb->stmt_init();
        try {
            $upload['stmt']->prepare($upload['query']);
            $upload['stmt']->bind_param('i', self::$config);
            $upload['stmt']->execute();
            $upload['stmt']->bind_result($type);
            $upload['result'] = $upload['stmt']->get_result();
            if ($upload['result']->num_rows != 0) {
                $upload['row'] = $upload['result']->fetch_assoc();
                return $upload['row']['type'];
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

    public function getUploadConfig()
    {
        $upload['query'] = 'SELECT image_library,thumbnail_height,thumbnail_width FROM upload_config WHERE id = ?';
        $upload['stmt'] = $this->connectdb->stmt_init();
        try {
            $upload['stmt']->prepare($upload['query']);
            $upload['stmt']->bind_param('i', self::$config);
            $upload['stmt']->execute();
            $upload['result'] = $upload['stmt']->get_result();
            if ($upload['result']->num_rows != 0) {
                $upload['row'] = $upload['result']->fetch_assoc();
                return $upload['row'];
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

    public function localUploadConfig()
    {
        $upload['query'] = 'SELECT local_dir,local_url,allowed_ext,disallowed_ext,max_size FROM upload_local WHERE id = ?';
        $upload['stmt'] = $this->connectdb->stmt_init();
        try {
            $upload['stmt']->prepare($upload['query']);
            $upload['stmt']->bind_param('i', self::$config);
            $upload['stmt']->execute();
            $upload['result'] = $upload['stmt']->get_result();
            if ($upload['result']->num_rows != 0) {
                $upload['row'] = $upload['result']->fetch_assoc();
                return $upload['row'];
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

    public function remoteUploadConfig()
    {
        $upload['query'] = 'SELECT 
        use_ssl,
        ftp_host,
        ftp_port,
        ftp_user,
        ftp_pw,
        pasv,
        remote_dir,
        remote_url,
        ftp_timeout,
        allowed_ext,
        disallowed_ext,
        max_size FROM upload_remote WHERE id = ?';
        $upload['stmt'] = $this->connectdb->stmt_init();
        try {
            $upload['stmt']->prepare($upload['query']);
            $upload['stmt']->bind_param('i', self::$config);
            $upload['stmt']->execute();
            $upload['result'] = $upload['stmt']->get_result();
            if ($upload['result']->num_rows != 0) {
                $upload['row'] = $upload['result']->fetch_assoc();
                return $upload['row'];
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
