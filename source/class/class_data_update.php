<?php
class DataUpdate
{
    private $config_id;

    public function __construct($connect)
    {
        $this->connectdb = $connect;
        $this->config_id = 1;
    }

    public function updateLastlogin($value, $uid)
    {
        $update_query = 'UPDATE user SET last_login = ? WHERE id = ?';
        $update_stmt = $this->connectdb->stmt_init();
        try {
            $update_stmt->prepare($update_query);
            $update_stmt->bind_param('si', 
                $value, 
                $uid
            );
            $update_stmt->execute();
        } catch (Exception $e) {
            echo '<h1>Service unavailable</h1>';
            echo '<br />';
            echo '<h2>Error Info :'.$e->getMessage().'</h2>';
            exit();
        }
    }

    public function updateConfig($name, $description)
    {
        $update_query = 'UPDATE config SET web_name = ?, web_description = ? WHERE id = ?';
        $update_stmt = $this->connectdb->stmt_init();
        try {
            $update_stmt->prepare($update_query);
            $update_stmt->bind_param('ssi', 
                $name, 
                $description,
                $this->config_id
            );
            $update_stmt->execute();
        } catch (Exception $e) {
            echo '<h1>Service unavailable</h1>';
            echo '<br />';
            echo '<h2>Error Info :'.$e->getMessage().'</h2>';
            exit();
        }
    }
}
