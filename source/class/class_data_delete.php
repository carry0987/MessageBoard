<?php
class DataDelete
{
    public function __construct($connect)
    {
        $this->connectdb = $connect;
    }

    public function deleteUser($uid)
    {
        $delete_query = 'DELETE FROM user WHERE id = ?';
        $delete_stmt = $this->connectdb->stmt_init();
        try {
            $delete_stmt->prepare($delete_query);
            $delete_stmt->bind_param('i', $uid);
            $delete_stmt->execute();
        } catch (Exception $e) {
            echo '<h1>Service unavailable</h1>';
            echo '<br />';
            echo '<h2>Error Info :'.$e->getMessage().'</h2>';
            exit();
        }
    }
}
