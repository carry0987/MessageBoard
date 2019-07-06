<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'data_update', 'forgot_password', 'email_config', 'email_template', 'social_config', 'captcha_config');
$load->loadFunction('filter', 'core', 'captcha');

//Template setting
$options = array(
    'template_dir' => 'template/common/',
    'css_dir' => 'static/css/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/',
    'auto_update' => true,
    'cache_lifetime' => 0,
    'cache_db' => $conn
);

$template = Template::getInstance();
$template->setOptions($options);

//Set Namespace for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Check login
if (!empty($login['username']) && $login['username'] !== false) {
    header('Location: '.$base_url);
}

//Get redirect url
if (isset($_SERVER['HTTP_REFERER'])) {
    $url_from = checkRedirect($_SERVER['HTTP_REFERER']);
} else {
    $url_from = false;
}

//Captcha Config
$captchaConfig = CaptchaConfig::getInstance();
$captchaConfig->getConnection($conn);
$captcha_apply = unserialize($captchaConfig->checkCaptchaApply());
if ($captchaConfig->checkCaptchaEnable() === true && in_array('login', $captcha_apply) === true) {
    $captcha['set'] = true;
    $captcha['type'] = $captchaConfig->checkCaptchaType();
    //Get Captcha Type
    switch ($captcha['type']) {
        case 'simple_captcha' === $captcha['type']:
            require dirname(__FILE__).'/source/class/class_simple_captcha.php';
            $simpleCaptcha = $captchaConfig->simpleCaptchaConfig();
            $captcha_option = getSimpleCaptchaOption($simpleCaptcha);
            $captcha_option['captcha_font'] = dirname(__FILE__).'/static/captcha/'.$captcha_option['captcha_font'];
            $setCaptcha = new SimpleCaptcha\SimpleCaptcha();
            $setCaptcha->setCaptchaOption($captcha_option);
            if (isset($_GET['rand'])) {
                if (checkReferer() !== true) {
                    exit('Access Denied');
                }
                $simpleCaptcha = $setCaptcha->generateCaptcha();
                $setCaptcha->getCaptchaImage($simpleCaptcha['captcha_image']);
                $_SESSION['simple_captcha'] = $simpleCaptcha['captcha_code'];
                exit();
            }
            break;
        case 'google_recaptcha' === $captcha['type']:
            require dirname(__FILE__).'/source/recaptcha/recaptcha.php';
            $reCaptcha = $captchaConfig->reCaptchaConfig();
            $siteKey = ($reCaptcha['site_key'] !== '') ? $reCaptcha['site_key'] : false;
            $secretKey = ($reCaptcha['secret_key'] !== '') ? $reCaptcha['secret_key'] : false;
            $resp = '';
            break;
        case 'svg_captcha' === $captcha['type']:
            require dirname(__FILE__).'/source/svg_captcha/SVGCaptcha.php';
            if ($captchaConfig->svgCaptchaConfig() !== false) {
                $svgCaptcha = $captchaConfig->svgCaptchaConfig();
                if (isset($_GET['svg_rand'])) {
                    if (checkReferer() !== true) {
                        exit('Access Denied');
                    }
                    $svg_difficulty = getSVGCaptchaOption($svgCaptcha['difficulty']);
                    $generateSVG = SVGCaptcha::getInstance($svgCaptcha['total_character'], $svgCaptcha['image_height'], $svgCaptcha['image_width'], 'black', $svg_difficulty);
                    $svg_captcha_code = $generateSVG->getSVGCaptcha();
                    echo $svg_captcha_code[1];
                    $_SESSION['svg_captcha'] = $svg_captcha_code[0];
                    exit();
                }
            } else {
                if (isset($_GET['svg_rand'])) {
                    if (checkReferer() !== true) {
                        exit('Access Denied');
                    }
                    $generateSVG = SVGCaptcha::getInstance(4, 100, 250, 'black', SVGCaptcha::EASY);
                    $svg_captcha_code = $generateSVG->getSVGCaptcha();
                    echo $svg_captcha_code[1];
                    $_SESSION['svg_captcha'] = $svg_captcha_code[0];
                    exit();
                }
            }
            break;
        default:
            break;
    }
} else {
    $captcha['set'] = false;
}

if (isset($_POST['submit']) || isset($_POST['check_simple_captcha']) || isset($_POST['check_svg_captcha'])) {
    if ($captcha['set'] === true) {
        //Check captcha
        switch ($captcha['type']) {
            case 'simple_captcha' === $captcha['type']:
                if (isset($_POST['check_simple_captcha'])) {
                    echo $setCaptcha->checkCaptcha($_SESSION['simple_captcha'], $_POST['check_simple_captcha']);
                    exit();
                }
                break;
            case 'google_recaptcha' === $captcha['type']:
                if (isset($_POST['g-recaptcha-response'])) {
                    $recaptcha = new ReCaptcha\ReCaptcha($secretKey);
                    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                    if ($resp->isSuccess() != true) {
                        echo '<script>alert('.$LANG['common']['captcha_error'].');location.href='.$base_url.'/login.php;</script>';
                        exit();
                    }
                }
                break;
            case 'svg_captcha' === $captcha['type']:
                if (isset($_POST['check_svg_captcha'])) {
                    if (strcasecmp($_SESSION['svg_captcha'], $_POST['check_svg_captcha']) !== 0) {
                        echo 0;
                        exit();
                    } elseif(strcasecmp($_SESSION['svg_captcha'], $_POST['check_svg_captcha']) === 0) {
                        echo 1;
                        exit();
                    }
                }
                break;
            default:
                break;
        }
    }
}

//Login with social account
$login_with = SocialAccount::getInstance();
$login_with->getConnection($conn);
//Check social login
if ($login_with->socialEnable() === true) {
    $social_login['type'] = $login_with->socialLoginType();
    if ($social_login['type'] !== false) {
        switch ($social_login['type']) {
            case 'github_login' === $social_login['type']:
                $load->loadSocialClass('github_oauth');
                $social_login['option'] = $login_with->githubConfig();
                if ($social_login['option'] !== false) {
                    $login_oauth = GithubOAuth::getInstance();
                    $login_oauth->setGithubOption($social_login['option']);
                    $social_login['set'] = true;
                } else {
                    $social_login['set'] = false;
                }
                break;
            case 'facebook_login' === $social_login['type']:
                break;
            case 'twitter_login' === $social_login['type']:
                break;
            default:
                $social_login['set'] = false;
                break;
        }
    }
} else {
    $social_login['set'] = false;
}

//Generate social login url
if (isset($_GET['login_with'])) {
    switch ($_GET['login_with']) {
        case 'github' === $_GET['login_with']:
            if ($social_login['set'] !== false) {
                switch ($social_login['type']) {
                    case 'github_login' === $social_login['type']:
                        if (!isset($_SESSION['github_access_token'])) {
                            //Generate a random hash and store in the session for security
                            $_SESSION['state'] = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
                            $social_login_url = htmlspecialchars($login_oauth->getAuthorizeURL($_SESSION['state']));
                            $_SESSION['login_set'] = 'Github';
                            header('Location: '.$social_login_url);
                        } elseif (isset($_SESSION['github_access_token'])) {
                            $github_access_token = $_SESSION['github_access_token'];
                            $github_user = $login_oauth->apiRequest($github_access_token);
                            //User profile data
                            $github_userData = array();
                            $github_userData['oauth_provider'] = 'github';
                            $github_userData['name'] = !empty($github_user->name) ? $github_user->name : '';
                            $github_userData['username'] = !empty($github_user->login) ? $github_user->login : '';
                            $github_userData['email'] = !empty($github_user->email) ? $github_user->email : '';
                        }
                        break;
                    default:
                        break;
                }
            }
            break;
        default:
            break;
    }
}

//Check social login
if (isset($_SESSION['login_set'])) {
    switch ($_SESSION['login_set']) {
        case 'Github' === $_SESSION['login_set']:
            if ($social_login['set'] !== false) {
                if (isset($_SESSION['github_access_token'])) {
                    $github_access_token = $_SESSION['github_access_token'];
                    $github_user = $login_oauth->apiRequest($github_access_token);
                    //User profile data
                    $github_userData = array();
                    $github_userData['oauth_provider'] = 'github';
                    $github_userData['name'] = !empty($github_user->name) ? $github_user->name : '';
                    $github_userData['username'] = !empty($github_user->login) ? $github_user->login : '';
                    $github_userData['email'] = !empty($github_user->email) ? $github_user->email : '';
                } else {
                    //Check social login access token
                    if (isset($_GET['code'])) {
                        //Verify the state matches the stored state
                        if (!$_GET['state'] || $_SESSION['state'] != $_GET['state']) {
                            header('Location: '.$_SERVER['PHP_SELF']);
                        }
                        //Exchange the auth code for a token
                        $github_access_token = $login_oauth->getAccessToken($_GET['state'], $_GET['code']);
                        $_SESSION['github_access_token'] = $github_access_token;
                        $_SESSION['login_with'] = 'github';
                        header('Location: '.$_SERVER['PHP_SELF']);
                    }
                }
            }
            break;
        default:
            break;
    }
}

//Check if forgot password
if (isset($_GET['pw'])) {
    if ($_GET['pw'] === 'forgot') {
        if (isset($_POST['email'])) {
            $forgotPassword = new ForgotPassword($conn);
            $display = 'view_success';
            //Check whether user exists in the database
            $forgot['email_exist'] = $forgotPassword->checkUserExist($_POST['email']);
            if ($forgot['email_exist'] !== false) {
                $email_error = '';
                $forgot['username'] = $forgotPassword->getUsername($_POST['email']);
                $uniqidStr = md5(uniqid(mt_rand()));
                $identity_timeout = time()+(7*24*60*60);
                if ($forgotPassword->checkForgotEmail($_POST['email']) === false) {
                    //Insert data with forgot pass code
                    $forgot['fp_code'] = $forgotPassword->insertForgotCode($uniqidStr, $identity_timeout, $_POST['email'], md5($forgot['username']));
                } else {
                    //Update data with forgot pass code
                    $forgot['fp_code'] = $forgotPassword->updateForgotCode($uniqidStr, $identity_timeout, $_POST['email'], md5($forgot['username']));
                }
                if ($forgot['fp_code'] === true) {
                    $resetPassLink = $base_url.'/login.php?pw=reset&user='.md5($forgot['username']).'&fp_code='.$uniqidStr;
                    //Send reset password email
                    $emailTemplate = EmailTemplate::getInstance();
                    $email_option = array('template_dir' => 'template/email/');
                    $emailTemplate->setOption($email_option);
                    $forgot['to'] = $forgot['email_exist'];
                    $forgot['subject'] = 'Password Update Request';
                    $mailParam = array(
                        'username' => $forgot['username'],
                        'resetURL' => $resetPassLink,
                        'web_name' => $meta['name']
                    );
                    $mailContent = $emailTemplate->loadEmailTemplate('forgot_password.html', $mailParam);
                    $mailContentNoHTML = $emailTemplate->loadEmailTemplate('forgot_password_no_html.html', $mailParam);
                    //Check email config
                    $emailConfig = EmailConfig::getInstance();
                    $emailConfig->getConnection($conn);
                    if ($emailConfig->checkEmailEnable() === true) {
                        require dirname(__FILE__).'/source/mailer/Exception.php';
                        require dirname(__FILE__).'/source/mailer/PHPMailer.php';
                        require dirname(__FILE__).'/source/mailer/SMTP.php';
                        if ($emailConfig->checkEmailType() !== false) {
                            //Instantiation and passing 'true' enables exceptions
                            $mail = new PHPMailer(true);
                            $mail->setLanguage(strtok($SYSTEM['system_lang'], '_'), 'source/mailer/language/');
                            $emailType = $emailConfig->checkEmailType();
                            switch ($emailType) {
                                case 'localhost' === $emailType:
                                    if ($emailConfig->localhostEmailConfig() !== false) {
                                        $email_localhost = $emailConfig->localhostEmailConfig();
                                        try {
                                            $mail->SMTPDebug = 0;
                                            if (function_exists('mail')) {
                                                $mail->isMail();
                                            } elseif (function_exists('sendmail')) {
                                                $mail->isSendmail();
                                            }
                                            $mail->CharSet = $email_localhost['charset'];
                                            $mail->setFrom($email_localhost['send_from'], $email_localhost['send_name']);
                                            $mail->addAddress($forgot['to'], $forgot['username']);
                                            $mail->isHTML(true);
                                            $mail->Subject = $forgot['subject'];
                                            $mail->Body = $mailContent;
                                            $mail->AltBody = $mailContentNoHTML;
                                            $mail->send();
                                            $email_process = true;
                                            $display = 'view_success';
                                        } catch (Exception $e) {
                                            $display = 'view_email_error';
                                        }
                                    }
                                    break;
                                case 'smtp' === $emailType:
                                    if ($emailConfig->smtpEmailConfig() !== false) {
                                        $email_smtp = $emailConfig->smtpEmailConfig();
                                        try {
                                            //Enable verbose debug output
                                            $mail->SMTPDebug = 0;
                                            $mail->isSMTP();
                                            //Specify main and backup SMTP servers
                                            $mail->Host = $email_smtp['smtp_host'];
                                            $mail->SMTPAuth = true;
                                            $mail->Username = $email_smtp['smtp_user'];
                                            $mail->Password = $email_smtp['smtp_pw'];
                                            $mail->SMTPSecure = 'tls';
                                            $mail->Port = 587;
                                            $mail->CharSet = $email_smtp['charset'];
                                            //Recipients
                                            $mail->setFrom($email_smtp['send_from'], $email_smtp['send_name']);
                                            $mail->addAddress($forgot['to'], $forgot['username']);
                                            //Set email format to HTML
                                            $mail->isHTML(true);
                                            $mail->Subject = $forgot['subject'];
                                            $mail->Body = $mailContent;
                                            $mail->AltBody = $mailContentNoHTML;
                                            $mail->send();
                                            $email_process = true;
                                            $display = 'view_success';
                                        } catch (Exception $e) {
                                            $display = 'view_email_error';
                                        }
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            } else {
                $display = 'view_account_error';
            }
        } else {
            $display = 'view_forgot_pw';
            $email_error = '';
        }
    } elseif ($_GET['pw'] === 'reset' && isset($_GET['fp_code'])) {
        if (isset($_GET['user'])) {
            $resetPassword = new ForgotPassword($conn);
            $reset['check_reset_code'] = $resetPassword->checkResetCode(time(), $_GET['user'], $_GET['fp_code']);
            if ($reset['check_reset_code'] === false) {
                $display = 'view_reset_error';
            } elseif ($reset['check_reset_code'] === true) {
                $display = 'view_reset_error';
                $resetPassword->deleteForgotByCode($_GET['fp_code']);
            } else {
                $reset['fp_code'] = $_GET['fp_code'];
                $display = 'view_reset_pw';
            }
        } elseif (isset($_POST['password']) && isset($_POST['pdr'])) {
            $reset_check = new ForgotPassword($conn);
            $reset['get_email'] = $reset_check->getUserEmail($_GET['fp_code']);
            $submit['password'] = input_filter($_POST['password']);
            $submit['password_confirm'] = input_filter($_POST['pdr']);
            $reset_permit = true;
            //Check password
            if ($submit['password'] == '' || $submit['password_confirm'] == '') {
                $back_page = '';
                $display = 'view_input_error';
                $reset_permit = false;
            } elseif ($submit['password_confirm'] !== $submit['password']) {
                $pdr_error = true;
                $back_page = '';
                $display = 'view_input_error';
                $reset_permit = false;
            }
            //Submit check
            if ($reset_permit === true) {
                $new_password = password_hash($submit['password'], PASSWORD_DEFAULT);
                if ($reset['get_email'] !== false) {
                    $updatePassword = DataUpdate::getInstance();
                    $updatePassword->getConnection($conn);
                    if ($updatePassword->updatePassword($new_password, $reset['get_email']) === true) {
                        $reset_process = true;
                        $display = 'view_success';
                        $reset_check->deleteForgotByEmail($reset['get_email']);
                    }
                } else {
                    $display = 'view_reset_error';
                }
            }
        }
    } else {
        header('Location: '.$base_url.'/login.php');
    }
} else {
    $account_error = $password_error = '';
    if (isset($_POST['submit'])) {
        $login_permit = true;
        if ($captcha['set'] === true) {
            switch ($captcha['type']) {
                case 'simple_captcha' === $captcha['type']:
                    if (isset($_POST['captcha_valid'])) {
                        if ($setCaptcha->checkCaptcha($_SESSION['simple_captcha'], $_POST['captcha_valid']) !== 1) {
                            $login_permit = false;
                            $captcha_error = true;
                        }
                    } else {
                        $login_permit = false;
                    }
                    break;
                case 'google_recaptcha' === $captcha['type']:
                    if (isset($_POST['g-recaptcha-response'])) {
                        $recaptcha = new ReCaptcha\ReCaptcha($secretKey);
                        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                        if ($resp->isSuccess() != true) {
                            $login_permit = false;
                            $captcha_error = true;
                            echo '<script>alert('.$LANG['common']['captcha_error'].');location.href='.$base_url.'/login.php;</script>';
                            exit();
                        }
                    } else {
                        $login_permit = false;
                    }
                    break;
                case 'svg_captcha' === $captcha['type']:
                    if (isset($_POST['captcha_valid'])) {
                        if (strcasecmp($_SESSION['svg_captcha'], $_POST['captcha_valid']) !== 0) {
                            $login_permit = false;
                            $captcha_error = true;
                        }
                    } else {
                        $login_permit = false;
                    }
                    break;
                default:
                    break;
            }
        }
        $account = $_POST['account'];
        $get_password = $_POST['password'];
        $login_query = 'SELECT uid,username,password,two_factor FROM user WHERE username = ? OR email = ?';
        $login_stmt = $conn->stmt_init();
        //Get account detail
        if ($login_stmt->prepare($login_query)) {
            $login_stmt->bind_param('ss', $account, $account);
            $login_stmt->execute();
            $login_stmt->bind_result($uid, $username, $password, $two_factor);
            $login_result = $login_stmt->get_result();
            while ($login_row = $login_result->fetch_assoc()) {
                $login_id = $login_row['uid'];
                $login_username = input_filter($login_row['username']);
                $check_password = $login_row['password'];
                $two_factor_set = $login_row['two_factor'];
            }
        } else {
            header('Location: '.$base_url);
            exit();
        }
        //Check result if exist
        if ($login_result->num_rows == 0) {
            $account_error = $LANG['common']['account_not_exist'];
            $login_permit = false;
        } elseif (password_verify($get_password, $check_password) === false) {
            $password_error = $LANG['common']['wrong_password'];
            $login_permit = false;
        }
        if ($login_permit === true) {
            $rememberMe = new RememberMe(SYSTEM_PATH);
            $rememberMe->getConnection($conn);
            $cookie_expiration_time = time() + (30 * 24 * 60 * 60);
            //Set Auth Cookies if 'Remember Me' checked
            if (!empty($_POST['remember_me'])) {
                $rememberMe->setCookie('user_login', $login_id, $cookie_expiration_time);
                $random_password = $rememberMe->getToken(16);
                $rememberMe->setCookie('random_pw', $random_password, $cookie_expiration_time);
                $random_pw_hash = password_hash($random_password, PASSWORD_DEFAULT);
                $expiry_date = $cookie_expiration_time;
                $selector = (isset($_COOKIE['random_selector'])) ? $_COOKIE['random_selector'] : 0;
                //Mark existing token as expired
                $userToken = $rememberMe->getTokenByUserID($login_id, $selector);
                if ($userToken !== false) {
                    $rememberMe->updateToken($login_id, $selector, $random_pw_hash);
                } else {
                    $random_selector = $rememberMe->getToken(16);
                    $rememberMe->setCookie('random_selector', $random_selector, $cookie_expiration_time);
                    //Insert new token
                    $rememberMe->insertToken($login_id, $random_selector, $random_pw_hash, $expiry_date);
                }
            } else {
                $rememberMe->clearAuthCookie();
            }
        }
        //Update last login date
        if ($login_result->num_rows != 0 && $login_permit === true) {
            $_SESSION['username'] = $login_username;
            $get_time = time();
            $update_user = DataUpdate::getInstance();
            $update_user->getConnection($conn);
            $update_user->updateLastlogin($get_time, $login_id);
            if (isset($_POST['redirect_url'])) {
                $url_from = $_POST['redirect_url'];
            } else {
                $url_from = false;
            }
            if (isset($_SESSION['login_bid'])) {
                header('Location: board.php?bid='.$_SESSION['login_bid'].'&action=create_article');
                unset($_SESSION['login_bid']);
            } else {
                $display = 'view_success';
            }
        } else {
            if (isset($captcha_error) && $captcha_error === true) {
                $display = 'view_captcha_error';
            } else {
                $display = 'view_login';
            }
        }
    } else {
        $display = 'view_login';
    }
}

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));
