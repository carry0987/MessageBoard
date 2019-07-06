<?php
class ForgotPassword
{
    private $connectdb;
    private $username;

    public function __construct($connect)
    {
        $this->connectdb = $connect;
    }

    public function checkUserExist($get_email)
    {
        $forgot['query'] = 'SELECT email FROM user WHERE email = ?';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('s', $get_email);
            $forgot['stmt']->execute();
            $forgot['stmt']->bind_result($email);
            $forgot['result'] = $forgot['stmt']->get_result();
            if ($forgot['result']->num_rows != 0) {
                return $get_email;
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

    public function getUserEmail($get_code)
    {
        $forgot['query'] = 'SELECT user_email FROM forgot_identity WHERE forgot_pass_identity = ?';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('s', $get_code);
            $forgot['stmt']->execute();
            $forgot['stmt']->bind_result($user_email);
            $forgot['result'] = $forgot['stmt']->get_result();
            if ($forgot['result']->num_rows != 0) {
                $forgot['row'] = $forgot['result']->fetch_assoc();
                return $forgot['row']['user_email'];
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

    public function getUsername($get_email)
    {
        $forgot['query'] = 'SELECT username FROM user WHERE email = ?';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('s', $get_email);
            $forgot['stmt']->execute();
            $forgot['stmt']->bind_result($username);
            $forgot['result'] = $forgot['stmt']->get_result();
            if ($forgot['result']->num_rows != 0) {
                $forgot['row'] = $forgot['result']->fetch_assoc();
                $this->username = $forgot['row']['username'];
                return $this->username;
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

    public function checkForgotEmail($get_email)
    {
        $forgot['query'] = 'SELECT user_email FROM forgot_identity WHERE user_email = ?';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('s', $get_email);
            $forgot['stmt']->execute();
            $forgot['stmt']->bind_result($user_email);
            $forgot['result'] = $forgot['stmt']->get_result();
            if ($forgot['result']->num_rows != 0) {
                return true;
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

    public function insertForgotCode($identity_code, $identity_timeout, $user_email, $md5_user)
    {
        $forgot['query'] = 'INSERT INTO forgot_identity (md5_username, user_email, forgot_pass_identity, forgot_timeout) VALUES (?,?,?,?)';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('ssss', 
                $md5_user, 
                $user_email, 
                $identity_code, 
                $identity_timeout
            );
            if ($forgot['stmt']->execute() === false) {
                return false;
            } else {
                return true;
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function updateForgotCode($identity_code, $identity_timeout, $user_email, $md5_user)
    {
        $forgot['query'] = 'UPDATE forgot_identity SET md5_username = ?, forgot_pass_identity = ?, forgot_timeout = ? WHERE user_email = ?';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('ssss', 
                $md5_user, 
                $identity_code, 
                $identity_timeout, 
                $user_email
            );
            if ($forgot['stmt']->execute() === false) {
                return false;
            } else {
                return true;
            }
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function checkResetCode($current_time, $get_user, $get_code)
    {
        $forgot['query'] = 'SELECT user_email, forgot_timeout FROM forgot_identity WHERE md5_username = ? AND forgot_pass_identity = ?';
        $forgot['stmt'] = $this->connectdb->stmt_init();
        try {
            $forgot['stmt']->prepare($forgot['query']);
            $forgot['stmt']->bind_param('ss', $get_user, $get_code);
            $forgot['stmt']->execute();
            $forgot['stmt']->bind_result($user_email, $forgot_timeout);
            $forgot['result'] = $forgot['stmt']->get_result();
            if ($forgot['result']->num_rows != 0) {
                $forgot['row'] = $forgot['result']->fetch_assoc();
                if ($forgot['row']['forgot_timeout'] > $current_time) {
                    return $forgot['row']['user_email'];
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

    public function deleteForgotByCode($get_code)
    {
        $delete_query = 'DELETE FROM forgot_identity WHERE forgot_pass_identity = ?';
        $delete_stmt = $this->connectdb->stmt_init();
        try {
            $delete_stmt->prepare($delete_query);
            $delete_stmt->bind_param('i', $get_code);
            $delete_stmt->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    public function deleteForgotByEmail($get_email)
    {
        $delete_query = 'DELETE FROM forgot_identity WHERE user_email = ?';
        $delete_stmt = $this->connectdb->stmt_init();
        try {
            $delete_stmt->prepare($delete_query);
            $delete_stmt->bind_param('i', $get_email);
            $delete_stmt->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }
}
