<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template', 'security_config', 'check', 'data_create', 'email_config', 'email_template', 'social_config');
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

//Captcha Config
$captchaConfig = SecurityConfig::getInstance();
$captchaConfig->getConnection($conn);
$captcha_apply = unserialize($captchaConfig->checkCaptchaApply());
if ($captchaConfig->checkCaptchaEnable() === true && in_array('signup', $captcha_apply) === true) {
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

//Check user exist
$account_check = Check::getInstance();
$account_check->getConnection($conn);
if (!empty($_GET['check_username'])) {
    echo $account_check->checkUsername($_GET['check_username']);
    exit();
} elseif (!empty($_GET['check_email'])) {
    echo $account_check->checkEmail($_GET['check_email']);
    exit();
}

//Check login
if (!empty($login['username']) && $login['username'] !== false) {
    header('Location: ./');
} else {
    $signup_permit = true;
    $username_error = $display_name_error = $password_error = $email_error = $captcha_error = '';
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
                        echo '<script>alert('.$LANG['common']['captcha_error'].');location.href='.$base_url.'/signup.php;</script>';
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

    //Check post
    $username = input_filter($_POST['username']);
    $password = input_filter($_POST['password']);
    $password_confirm = input_filter($_POST['pdr']);
    $get_email = input_filter($_POST['email']);
    $user_language = input_filter((!empty($_POST['user_lang'])) ?: 'en_US');
    $get_time = time();

    //Check display name
    if (empty($_POST['display_name'])) {
        $display_name_error = $LANG['common']['display_name_empty'];
        $signup_permit = false;
    } else {
        $get_display_name = input_filter($_POST['display_name']);
    }

    //Check username
    if (empty($_POST['username'])) {
        $username_error = $LANG['common']['username_empty'];
        $signup_permit = false;
    } elseif (!empty($_GET['check_username'])) {
        $username_clear = str_replace(' ', '_', $_GET['check_username']);
        $username_clear = preg_replace( '/[^0-9A-Za-z_]/', '', $_GET['check_username']);
        if ($account_check->checkUsername($username_clear) === false) {
            $username_error = $LANG['common']['duplicate_username'];
            $signup_permit = false;
        }
    }

    //Check password
    if (empty($password)) {
        $password_error = $LANG['common']['password_empty'];
        $signup_permit = false;
    } elseif (!empty($password) && !empty($password_confirm)) {
        if ($password_confirm !== $password) {
            $password_error = $LANG['common']['repassword_error'];
            $signup_permit = false;
        }
    }

    //Check email
    if (empty($get_email)) {
        $email_error = $LANG['common']['email_empty'];
        $signup_permit = false;
    } elseif (!preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/', $get_email)) {
        $email_error = $LANG['common']['email_format_error'];
        $signup_permit = false;
    } elseif (!empty($_GET['check_email'])) {
        if ($account_check->checkEmail($_GET['check_email']) === false) {
            $email_error = $LANG['common']['duplicate_email'];
            $signup_permit = false;
        }
    } elseif (strlen($get_email) > 80) {
        $email_error = $LANG['common']['email_length_error'];
        $signup_permit = false;
    }

    //Check email domain
    $checkDomain = SecurityConfig::getInstance();
    $checkDomain->getConnection($conn);
    $getDomainConfig = $checkDomain->getDomainConfig();
    $checkAllow = explode('|', $getDomainConfig['allow_domain']);
    $checkDisallow = explode('|', $getDomainConfig['disallow_domain']);
    $getDomain = substr($get_email, strpos($get_email, '@') + 1);
    if (in_array($getDomain, $checkAllow) === false) {
        $email_error = $LANG['common']['email_format_error'];
        $signup_permit = false;
    }
    if (in_array($getDomain, $checkDisallow) === true) {
        $email_error = $LANG['common']['email_format_error'];
        $signup_permit = false;
    }

    //Check language
    if (empty($user_language)) {
        $user_language = 'en_US';
        $signup_permit = false;
    } elseif (!empty($user_language)) {
        switch ($user_language) {
            case 'en_US':
                $user_language = 'en_US';
                break;
            case 'zh_TW':
                $user_language = 'zh_TW';
                break;
            case 'ja_JP':
                $user_language = 'ja_JP';
                break;
            case 'th_TH':
                $user_language = 'th_TH';
                break;
            default:
                $user_language = 'en_US';
                break;
        }
    }

    if ($captcha['set'] === true) {
        switch ($captcha['type']) {
            case 'simple_captcha' === $captcha['type']:
                if (isset($_POST['captcha_valid'])) {
                    if ($setCaptcha->checkCaptcha($_SESSION['simple_captcha'], $_POST['captcha_valid']) !== 1) {
                        $signup_permit = false;
                        $display = 'view_captcha_error';
                    }
                } else {
                    $signup_permit = false;
                }
                break;
            case 'google_recaptcha' === $captcha['type']:
                if (isset($_POST['g-recaptcha-response'])) {
                    $recaptcha = new ReCaptcha\ReCaptcha($secretKey);
                    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                    if ($resp->isSuccess() != true) {
                        $signup_permit = false;
                        $display = 'view_captcha_error';
                    }
                } else {
                    $signup_permit = false;
                }
                break;
            case 'svg_captcha' === $captcha['type']:
                if (isset($_POST['captcha_valid'])) {
                    if (strcasecmp($_SESSION['svg_captcha'], $_POST['captcha_valid']) !== 0) {
                        $signup_permit = false;
                        $display = 'view_captcha_error';
                    }
                } else {
                    $signup_permit = false;
                }
                break;
            default:
                $signup_permit = false;
                break;
        }
    }

    //Submit check
    if ($signup_permit === true && !empty($username) && !empty($password) && !empty($get_email)) {
        $insert_password = password_hash($password, PASSWORD_DEFAULT);
        if ($account_check->checkUsername($username) === false) {
                $username_error = $LANG['common']['duplicate_username'];
                $signup_permit = false;
        } elseif ($account_check->checkEmail($get_email) === false) {
                $email_error = $LANG['common']['duplicate_email'];
                $signup_permit = false;
        }

        //Check email config
        $emailConfig = EmailConfig::getInstance();
        $emailConfig->getConnection($conn);
        if ($emailConfig->checkEmailEnable() === true) {
            //Get email template
            $emailTemplate = EmailTemplate::getInstance();
            $email_option = array('template_dir' => 'template/email/');
            $emailTemplate->setOption($email_option);
            $welcome_msg['to'] = $get_email;
            $welcome_msg['subject'] = $LANG['common']['welcome'];
            $mailParam = array(
                'welcome_to' => $LANG['common']['welcome_to'],
                'web_name' => $meta['name'],
                'dear' => $LANG['common']['dear'],
                'username' => $username
            );
            $mailContent = $emailTemplate->loadEmailTemplate('welcome_email.html', $mailParam);
            $mailContentNoHTML = $emailTemplate->loadEmailTemplate('welcome_email_no_html.html', $mailParam);
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
                                $mail->addAddress($welcome_msg['to'], $username);
                                $mail->isHTML(true);
                                $mail->Subject = $welcome_msg['subject'];
                                $mail->Body = $mailContent;
                                $mail->AltBody = $mailContentNoHTML;
                                $mail->send();
                                $signup_permit = true;
                                $display = 'view_success';
                            } catch (Exception $e) {
                                $signup_permit = false;
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
                                $mail->addAddress($welcome_msg['to'], $username);
                                //Set email format to HTML
                                $mail->isHTML(true);
                                $mail->Subject = $welcome_msg['subject'];
                                $mail->Body = $mailContent;
                                $mail->AltBody = $mailContentNoHTML;
                                $mail->send();
                                $signup_permit = true;
                                $display = 'view_success';
                            } catch (Exception $e) {
                                $signup_permit = false;
                                $display = 'view_email_error';
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        //Check signup permit
        if ($signup_permit === true) {
            $user_info = array(
                'display_name' => $get_display_name,
                'username' => $username,
                'password' => $insert_password,
                'bio' => '',
                'email' => $get_email,
                'language' => $user_language,
                'is_admin' => 0,
                'online_status' => time()+5,
                'last_login' => $get_time,
                'join_date' => $get_time
            );
            $create_user = DataCreate::getInstance();
            $create_user->getConnection($conn);
            $create_user->createUser($user_info);
            $display = 'view_success';
        } else {
            $display = 'view_signup_error';
        }
    } else {
        $display = 'view_signup_error';
    }
} else {
    $display = 'view_signup';
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

include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));
