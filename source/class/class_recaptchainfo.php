<?php
class ReCaptchaInfo
{
    private $connectdb;
    private $recpatcha_config;

    public function __construct($connect)
    {
        $this->connectdb = $connect;
        $this->recpatcha_config = 1;
    }

    public function reCaptchaSite()
    {
        $recpatcha_query = 'SELECT site_key FROM recaptcha WHERE id = ?';
        $recpatcha_stmt = $this->connectdb->stmt_init();
        try {
            $recpatcha_stmt->prepare($recpatcha_query);
            $recpatcha_stmt->bind_param('i', $this->recpatcha_config);
            $recpatcha_stmt->execute();
            $recpatcha_stmt->bind_result($site_key);
            $recpatcha_result = $recpatcha_stmt->get_result();
            if ($recpatcha_result->num_rows != 0) {
                $recpatcha_row = $recpatcha_result->fetch_assoc();
                $this->site_key = $recpatcha_row['site_key'];
                return $this->site_key;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function reCaptchaSecret()
    {
        $recpatcha_query = 'SELECT secret_key FROM recaptcha WHERE id = ?';
        $recpatcha_stmt = $this->connectdb->stmt_init();
        try {
            $recpatcha_stmt->prepare($recpatcha_query);
            $recpatcha_stmt->bind_param('i', $this->recpatcha_config);
            $recpatcha_stmt->execute();
            $recpatcha_stmt->bind_result($secret_key);
            $recpatcha_result = $recpatcha_stmt->get_result();
            if ($recpatcha_result->num_rows != 0) {
                $recpatcha_row = $recpatcha_result->fetch_assoc();
                $this->secret_key = $recpatcha_row['secret_key'];
                return $this->secret_key;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
