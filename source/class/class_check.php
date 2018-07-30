<?php
class Check
{
    private $connectdb;

    public function __construct($connect)
    {
        $this->connectdb = $connect;
    }

    public function checkUsername($get_username)
    {
        $username_query = 'SELECT username FROM user WHERE username = ?';
        $username_stmt = $this->connectdb->stmt_init();
        try {
            $username_stmt->prepare($username_query);
            $username_stmt->bind_param('s', $get_username);
            $username_stmt->execute();
            $username_stmt->bind_result($username);
            $username_result = $username_stmt->get_result();
            if ($username_result->num_rows != 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function checkEmail($get_email)
    {
        $email_query = 'SELECT email FROM user WHERE email = ?';
        $email_stmt = $this->connectdb->stmt_init();
        try {
            $email_stmt->prepare($email_query);
            $email_stmt->bind_param('s', $get_email);
            $email_stmt->execute();
            $email_stmt->bind_result($email);
            $email_result = $email_stmt->get_result();
            if ($email_result->num_rows != 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
