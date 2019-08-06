<?php
class SecurityConfig
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

    public function getDomainConfig()
    {
        $domain['query'] = 'SELECT allow_domain, disallow_domain FROM email_config WHERE id = ?';
        $domain['stmt'] = $this->connectdb->stmt_init();
        try {
            $domain['stmt']->prepare($domain['query']);
            $domain['stmt']->bind_param('i', self::$config);
            $domain['stmt']->execute();
            $domain['stmt']->bind_result($allow_domain, $disallow_domain);
            $domain['result'] = $domain['stmt']->get_result();
            if ($domain['result']->num_rows != 0) {
                $domain['row'] = $domain['result']->fetch_assoc();
                return $domain['row'];
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

    public function checkCaptchaEnable()
    {
        $captcha['query'] = 'SELECT enable FROM captcha_config WHERE id = ?';
        $captcha['stmt'] = $this->connectdb->stmt_init();
        try {
            $captcha['stmt']->prepare($captcha['query']);
            $captcha['stmt']->bind_param('i', self::$config);
            $captcha['stmt']->execute();
            $captcha['stmt']->bind_result($enable);
            $captcha['result'] = $captcha['stmt']->get_result();
            if ($captcha['result']->num_rows != 0) {
                $captcha['row'] = $captcha['result']->fetch_assoc();
                if ($captcha['row']['enable'] !== 1) {
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

    public function checkCaptchaType()
    {
        $captcha['query'] = 'SELECT type FROM captcha_config WHERE id = ?';
        $captcha['stmt'] = $this->connectdb->stmt_init();
        try {
            $captcha['stmt']->prepare($captcha['query']);
            $captcha['stmt']->bind_param('i', self::$config);
            $captcha['stmt']->execute();
            $captcha['stmt']->bind_result($type);
            $captcha['result'] = $captcha['stmt']->get_result();
            if ($captcha['result']->num_rows != 0) {
                $captcha['row'] = $captcha['result']->fetch_assoc();
                return $captcha['row']['type'];
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

    public function checkCaptchaApply()
    {
        $captcha['query'] = 'SELECT apply FROM captcha_config WHERE id = ?';
        $captcha['stmt'] = $this->connectdb->stmt_init();
        try {
            $captcha['stmt']->prepare($captcha['query']);
            $captcha['stmt']->bind_param('i', self::$config);
            $captcha['stmt']->execute();
            $captcha['result'] = $captcha['stmt']->get_result();
            if ($captcha['result']->num_rows != 0) {
                $captcha['row'] = $captcha['result']->fetch_assoc();
                return $captcha['row']['apply'];
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

    public function simpleCaptchaConfig()
    {
        $captcha['query'] = 'SELECT image_height, image_width, font_file, text_color, noise_color, total_character, random_dots, random_lines, check_sensitive FROM simple_captcha WHERE id = ?';
        $captcha['stmt'] = $this->connectdb->stmt_init();
        try {
            $captcha['stmt']->prepare($captcha['query']);
            $captcha['stmt']->bind_param('i', self::$config);
            $captcha['stmt']->execute();
            $captcha['stmt']->bind_result($image_height, $image_width, $font_file, $text_color, $noise_color, $total_character, $random_dots, $random_lines, $check_sensitive);
            $captcha['result'] = $captcha['stmt']->get_result();
            if ($captcha['result']->num_rows != 0) {
                $captcha['row'] = $captcha['result']->fetch_assoc();
                return $captcha['row'];
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

    public function reCaptchaConfig()
    {
        $captcha['query'] = 'SELECT site_key,secret_key FROM google_recaptcha WHERE id = ?';
        $captcha['stmt'] = $this->connectdb->stmt_init();
        try {
            $captcha['stmt']->prepare($captcha['query']);
            $captcha['stmt']->bind_param('i', self::$config);
            $captcha['stmt']->execute();
            $captcha['stmt']->bind_result($site_key, $secret_key);
            $captcha['result'] = $captcha['stmt']->get_result();
            if ($captcha['result']->num_rows != 0) {
                $captcha['row'] = $captcha['result']->fetch_assoc();
                return $captcha['row'];
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

    public function svgCaptchaConfig()
    {
        $captcha['query'] = 'SELECT image_height, image_width, total_character, difficulty FROM svg_captcha WHERE id = ?';
        $captcha['stmt'] = $this->connectdb->stmt_init();
        try {
            $captcha['stmt']->prepare($captcha['query']);
            $captcha['stmt']->bind_param('i', self::$config);
            $captcha['stmt']->execute();
            $captcha['stmt']->bind_result($image_height, $image_width, $total_character, $difficulty);
            $captcha['result'] = $captcha['stmt']->get_result();
            if ($captcha['result']->num_rows != 0) {
                $captcha['row'] = $captcha['result']->fetch_assoc();
                return $captcha['row'];
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
