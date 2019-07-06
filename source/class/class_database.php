<?php
class Database
{
    private $connectdb;
    private static $instance = null;

    // Get an instance of the Database
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Constructor
    private function __construct()
    {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            //Handle Exception of MySQLi
            $driver = new mysqli_driver();
            $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
            $this->connectdb = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
            $this->connectdb->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone()
    {
    }

    // Get mysqli connection
    public function getConnection()
    {
        return $this->connectdb;
    }
}
