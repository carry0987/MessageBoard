<?php 
class RememberMe
{
    private $path = '/';
    private $connectDB;

    public function __construct($path)
    {
        $this->path = rtrim($path, '/\\');
    }

    public function getConnection($connect)
    {
        $this->connectDB = $connect;
    }

    public function checkUserInfo($userID, $selector, $randomPW)
    {
        $current_time = time();
        $userID = (int) $userID;
        $result = false;
        //Initiate auth token verification diirective to false
        $isPasswordVerified = false;
        $isExpiryDateVerified = false;
        //Get token for username
        $userToken = $this->getTokenByUserID($userID, $selector);
        if ($userToken !== false) {
            //Validate random password cookie with database
            if (password_verify($randomPW, $userToken['password_hash'])) {
                $isPasswordVerified = true;
            }
            //Check cookie expiration by date
            if ($userToken['expiry_date'] >= $current_time) {
                $isExpiryDareVerified = true;
            }
        }
        //Redirect if all cookie based validation retuens true
        //Else, mark the token as expired and clear cookies
        if ($userToken !== false && $isPasswordVerified === true && $isExpiryDareVerified === true) {
            $result = $userToken;
        } else {
            if ($userToken !== false) {
                $this->resetToken($selector);
                //$result = $userToken;
            } else {
                $result = false;
            }
            //Clear cookies
            $this->clearAuthCookie();
        }
        return $result;
    }

    public function getToken($length)
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    public function clearAuthCookie()
    {
        if (isset($_COOKIE['random_pw'])) {
            $this->setCookie('random_pw', 'none');
        }
    }

    public function setCookie($cookieName, $value, $cookieTime = 0)
    {
        $security = (isset($_SERVER['HTTPS'])) ? true : false;
        setcookie($cookieName, $value, $cookieTime, $this->path.'/', null, $security, true);
    }

    public function getTokenByUserID($userID, $selector)
    {
        $query = 'SELECT password_hash,expiry_date,user.username FROM remember_me 
        INNER JOIN user ON user.uid = remember_me.user_id 
        WHERE user_id = ? AND selector_hash = ?';
        $stmt = $this->connectDB->stmt_init();
        try {
            $stmt->prepare($query);
            $stmt->bind_param('is', $userID, $selector);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0) {
                while ($row = $result->fetch_assoc()) {
                    $results = $row;
                }
            } else {
                $results = false;
            }
            return $results;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            return false;
        }
    }

    public function resetToken($selector)
    {
        $query = 'UPDATE remember_me SET password_hash = ?, expiry_date = ? WHERE selector_hash = ?';
        $stmt = $this->connectDB->stmt_init();
        $empty = '';
        $date = 0;
        try {
            $stmt->prepare($query);
            $stmt->bind_param('sis', $empty, $date, $selector);
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            return false;
        }
    }

    public function updateToken($userID, $selector, $pw_hash)
    {
        $query = 'UPDATE remember_me SET password_hash = ?, expiry_date = ? WHERE user_id = ? AND selector_hash = ?';
        $stmt = $this->connectDB->stmt_init();
        $getTime = time() + (30 * 24 * 60 * 60);
        try {
            $stmt->prepare($query);
            $stmt->bind_param('siis', $pw_hash, $getTime, $userID, $selector);
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            return false;
        }
    }

    public function insertToken($userID, $selector, $random_password_hash, $expiry_date = 0)
    {
        $query = 'INSERT INTO remember_me (user_id, selector_hash, password_hash, expiry_date) VALUES (?, ?, ?, ?)';
        $stmt = $this->connectDB->stmt_init();
        try {
            $stmt->prepare($query);
            $stmt->bind_param('issi', $userID, $selector, $random_password_hash, $expiry_date);
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            return false;
        }
    }

    private function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            //Not so random
            return $min;
        }
        $log = ceil(log($range, 2));
        //Length in bytes
        $bytes = (int) ($log / 8) + 1;
        //Length in bits
        $bits = (int) $log + 1;
        //Set all lower bits to 1
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            //Discard irrelevant bits
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);
        return $min + $rnd;
    }
}
