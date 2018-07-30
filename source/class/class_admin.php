<?php
class Admin
{
    private $config_id;

    public function __construct($connect)
    {
        $this->connectdb = $connect;
        $this->config_id = (int) 1;
    }

    public function showConfig()
    {
        $admin_query = 'SELECT web_name,web_description FROM config WHERE id = ?';
        $admin_stmt = $this->connectdb->stmt_init();
        try {
            $admin_stmt->prepare($admin_query);
            $admin_stmt->bind_param('i', $this->config_id);
            $admin_stmt->execute();
            $admin_stmt->bind_result($web_name, $web_description);
            $admin_result = $admin_stmt->get_result();
            while ($admin_row = $admin_result->fetch_assoc()) {
                $config['name'] = $admin_row['web_name'];
                $config['description'] = $admin_row['web_description'];
            }
        } catch (Exception $e) {
            echo '<h1>Service unavailable</h1>';
            echo '<br />';
            echo '<h2>Error Info :'.$e->getMessage().'</h2>';
            exit();
        }
        return $config;
    }
}
