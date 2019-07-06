<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
//Protect script
define('IN_ADMIN', true);
$load->loadClass('template', 'pagination', 'check', 'data_create', 'data_read', 'data_update', 'data_delete', 'admin', 'social_config', 'minifier', 'thumbnail');
$load->loadFunction('filter', 'admin', 'core');

//Require another language file
$load_language->addLanguageFile(array('admin-upload', 'admin-captcha', 'admin-social', 'admin-seo', 'admin-email'));
$lang_file = $load_language->loadLanguage($SYSTEM['system_lang']);
foreach ($lang_file as $lang) {
    require ROOT_PATH.$lang;
}

//Template setting
$options = array(
    'template_dir' => 'template/admin/',
    'css_dir' => 'static/css/admin/',
    'js_dir' => 'static/js/admin/',
    'cache_dir' => 'data/cache/admin/',
    'auto_update' => true,
    'cache_lifetime' => 0,
    'cache_db' => $conn
);

$template = Template::getInstance();
$template->setOptions($options);

//Set Namespace for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Breadcrumb
$admin_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Check mod
$content['global'] = '';
$content['member'] = '';
$content['article'] = '';
$content['board'] = '';
$content['category'] = '';
$content['upload'] = '';
$content['social'] = '';
$content['email'] = '';
$content['seo'] = '';
$content['captcha'] = '';
$content['database'] = '';

//List Language Options
$web_lang_list = array(
    'en_US' => 'English',
    'zh_TW' => '繁體中文',
    'ja_JP' => '日本語',
    'th_TH' => 'Thai'
);

if (!empty($login['username']) && $login['admin'] === true) {
    $show_admin = true;
    if (isset($_GET['mod'])) {
        switch ($_GET['mod']) {
            case ($_GET['mod'] === 'global'):
                $content['global'] = 'active';
                $display = 'view_global';
                $adminConfig = DataRead::getInstance();
                $adminConfig->getConnection($conn);
                $read_query = array(
                    'config' => 'SELECT web_name,web_description,web_language FROM global_config WHERE id = ?'
                );
                $globalConfig = $adminConfig->getConfig($read_query);
                break;
            case ($_GET['mod'] === 'member'):
                $content['member'] = 'active';
                $display = 'manage_member';
                $memberConfig = Admin::getInstance();
                $memberConfig->getConnection($conn);
                if (isset($_GET['show_admin']) && $_GET['show_admin'] === 'yes') {
                    $members = $memberConfig->showAdmin();
                } else {
                    $members = $memberConfig->showData('member');
                }
                if (isset($_POST['keyword']) && isset($_POST['sortBy']) && !isset($_POST['delete'])) {
                    $memberList = $memberConfig->searchData('member', $_POST['keyword'], $_POST['sortBy']);
                    //Check page value
                    $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                    $itemsPerPage = 10;
                    $urlPattern = 'admin.php?mod=member&amp;page=(:num)';
                    $paginator = new Pagination($memberList, $itemsPerPage, $currentPage, $urlPattern);
                    $member_result = $paginator->getResults();
                    $total_pages = $paginator->getNumPages();
                    $total_member = $paginator->getTotalItems();
                    include($template->loadTemplate('ajax_member_list.html'));
                    exit();
                }
                //Check page value
                if (!isset($_POST['page'])) {
                    $currentPage = (!empty($_GET['page'])) ? input_filter(checkPage($_GET['page'])) : 1;
                }
                $itemsPerPage = 10;
                if ($currentPage > countTotalPage($members, $itemsPerPage)) {
                    header('Location: admin.php?mod=member&page='.countTotalPage($members, $itemsPerPage));
                }
                $urlPattern = 'admin.php?mod=member&amp;page=(:num)';
                $paginator = new Pagination($members, $itemsPerPage, $currentPage, $urlPattern);
                $member_result = $paginator->getResults();
                break;
            case ($_GET['mod'] === 'article'):
                $content['article'] = 'active';
                $display = 'manage_article';
                $articleConfig = Admin::getInstance();
                $articleConfig->getConnection($conn);
                $articleProperty = array('0' => 'article', '3' => 'pinned');
                if (isset($_GET['show_pinned']) && $_GET['show_pinned'] === 'yes') {
                    $articles = $articleConfig->showPinned('DESC');
                } else {
                    $articles = $articleConfig->showData('article', 'DESC');
                }
                if (isset($_POST['keyword']) && isset($_POST['sortBy']) && !isset($_POST['delete'])) {
                    $articleList = $articleConfig->searchData('article', $_POST['keyword'], $_POST['sortBy']);
                    //Check page value
                    $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                    $itemsPerPage = 10;
                    $urlPattern = 'admin.php?mod=article&amp;page=(:num)';
                    $paginator = new Pagination($articleList, $itemsPerPage, $currentPage, $urlPattern);
                    $article_result = ($paginator->getResults() !== false) ? $paginator->getResults() : array();
                    foreach ($article_result as $key => $value) {
                        $article_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    }
                    $total_pages = $paginator->getNumPages();
                    $total_article = $paginator->getTotalItems();
                    include($template->loadTemplate('ajax_article_list.html'));
                    exit();
                }
                //Get board list
                if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                    $boardList = $articleConfig->getOptionList($_POST['category_id']);
                    include($template->loadTemplate('ajax_option.html'));
                    exit();
                } else {
                    $categoryList = $articleConfig->getOptionList();
                }
                //Check page value
                $currentPage = (!empty($_GET['page'])) ? input_filter(checkPage($_GET['page'])) : 1;
                $itemsPerPage = 10;
                if ($currentPage > countTotalPage($articles, $itemsPerPage) && $articles !== false) {
                    header('Location: admin.php?mod=article&page='.countTotalPage($articles, $itemsPerPage));
                }
                $urlPattern = 'admin.php?mod=article&amp;page=(:num)';
                $paginator = new Pagination($articles, $itemsPerPage, $currentPage, $urlPattern);
                $article_result = ($paginator->getResults() !== false) ? $paginator->getResults() : array();
                foreach ($article_result as $key => $value) {
                    $article_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                }
                break;
            case ($_GET['mod'] === 'board'):
                $content['board'] = 'active';
                $display = 'manage_board';
                $boardConfig = Admin::getInstance();
                $boardConfig->getConnection($conn);
                $boards = $boardConfig->showData('board');
                if (isset($_POST['keyword']) && isset($_POST['sortBy']) && !isset($_POST['delete'])) {
                    $boardList = $boardConfig->searchData('board', $_POST['keyword'], $_POST['sortBy']);
                    //Check page value
                    $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                    $itemsPerPage = 10;
                    $urlPattern = 'admin.php?mod=board&amp;page=(:num)';
                    $paginator = new Pagination($boardList, $itemsPerPage, $currentPage, $urlPattern);
                    $board_result = $paginator->getResults();
                    $total_pages = $paginator->getNumPages();
                    $total_board = $paginator->getTotalItems();
                    include($template->loadTemplate('ajax_board_list.html'));
                    exit();
                }
                //Get category list
                $categoryList = $boardConfig->getOptionList();
                //Check page value
                $currentPage = (!empty($_GET['page'])) ? input_filter(checkPage($_GET['page'])) : 1;
                $itemsPerPage = 10;
                if ($currentPage > countTotalPage($boards, $itemsPerPage) && $boards !== false) {
                    header('Location: admin.php?mod=board&page='.countTotalPage($boards, $itemsPerPage));
                }
                $urlPattern = 'admin.php?mod=board&amp;page=(:num)';
                $paginator = new Pagination($boards, $itemsPerPage, $currentPage, $urlPattern);
                $board_result = ($paginator->getResults() !== false) ? $paginator->getResults() : array();
                foreach ($board_result as $key => $value) {
                    $board_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $board_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                break;
            case ($_GET['mod'] === 'category'):
                $content['category'] = 'active';
                $display = 'manage_category';
                $categoryConfig = Admin::getInstance();
                $categoryConfig->getConnection($conn);
                $categorys = $categoryConfig->showData('category');
                if (isset($_POST['keyword']) && isset($_POST['sortBy']) && !isset($_POST['delete'])) {
                    $categoryList = $categoryConfig->searchData('category', $_POST['keyword'], $_POST['sortBy']);
                    //Check page value
                    $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                    $itemsPerPage = 10;
                    $urlPattern = 'admin.php?mod=category&amp;page=(:num)';
                    $paginator = new Pagination($categoryList, $itemsPerPage, $currentPage, $urlPattern);
                    $category_result = $paginator->getResults();
                    $total_pages = $paginator->getNumPages();
                    $total_category = $paginator->getTotalItems();
                    include($template->loadTemplate('ajax_category_list.html'));
                    exit();
                }
                //Get category list
                $categoryList = $categoryConfig->getOptionList();
                //Check page value
                $currentPage = (!empty($_GET['page'])) ? input_filter(checkPage($_GET['page'])) : 1;
                $itemsPerPage = 10;
                if ($currentPage > countTotalPage($categorys, $itemsPerPage) && $categorys !== false) {
                    header('Location: admin.php?mod=category&page='.countTotalPage($categorys, $itemsPerPage));
                }
                $urlPattern = 'admin.php?mod=category&amp;page=(:num)';
                $paginator = new Pagination($categorys, $itemsPerPage, $currentPage, $urlPattern);
                $category_result = ($paginator->getResults() !== false) ? $paginator->getResults() : array();
                foreach ($category_result as $key => $value) {
                    $category_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $category_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                break;
            case ($_GET['mod'] === 'upload'):
                $load->loadClass('upload_config');
                $content['upload'] = 'active';
                $display = 'config_upload';
                $uploadConfig = UploadConfig::getInstance();
                $uploadConfig->getConnection($conn);
                if ($uploadConfig->checkUploadEnable() === true) {
                    $upload['set'] = true;
                } else {
                    $upload['set'] = false;
                }
                $uploadMainConfig = $uploadConfig->getUploadConfig();
                $upload['current_library'] = $uploadMainConfig['image_library'];
                $upload['thumbnail_height'] = $uploadMainConfig['thumbnail_height'];
                $upload['thumbnail_width'] = $uploadMainConfig['thumbnail_width'];
                $upload['current_type'] = $uploadConfig->checkUploadType();
                $upload_type_list = array('local', 'remote');
                $maxInput = array(
                    'local_dir' => 250, 
                    'local_url' => 250, 
                    'max_size' => 8, 
                    'ftp_host' => 250, 
                    'ftp_port' => 5, 
                    'ftp_user' => 50, 
                    'ftp_pw' => 256,
                    'remote_dir' => 250,
                    'remote_url' => 250,
                    'ftp_timeout' => 8
                );
                //Get Upload Type
                if (isset($_POST['show_upload_type'])) {
                    $upload['type'] = $_POST['show_upload_type'];
                    switch ($upload['type']) {
                        case 'local' === $upload['type']:
                            if ($uploadConfig->localUploadConfig() !== false) {
                                $localUpload = $uploadConfig->localUploadConfig();
                                $upload_option = array(
                                    'local_dir' => $localUpload['local_dir'],
                                    'local_url' => $localUpload['local_url'],
                                    'allowed_ext' => $localUpload['allowed_ext'],
                                    'disallowed_ext' => $localUpload['disallowed_ext'],
                                    'max_size' => $localUpload['max_size']
                                );
                            }
                            break;
                        case 'remote' === $upload['type']:
                            if ($uploadConfig->remoteUploadConfig() !== false) {
                                $remoteUpload = $uploadConfig->remoteUploadConfig();
                                $upload_option = array(
                                    'use_ssl' => $remoteUpload['use_ssl'],
                                    'ftp_host' => $remoteUpload['ftp_host'],
                                    'ftp_port' => $remoteUpload['ftp_port'],
                                    'ftp_user' => $remoteUpload['ftp_user'],
                                    'ftp_pw' => $remoteUpload['ftp_pw'],
                                    'pasv' => $remoteUpload['pasv'],
                                    'remote_dir' => $remoteUpload['remote_dir'],
                                    'remote_url' => $remoteUpload['remote_url'],
                                    'ftp_timeout' => $remoteUpload['ftp_timeout'],
                                    'allowed_ext' => $remoteUpload['allowed_ext'],
                                    'disallowed_ext' => $remoteUpload['disallowed_ext'],
                                    'max_size' => $remoteUpload['max_size']
                                );
                            }
                            break;
                        default:
                            break;
                    }
                    include($template->loadTemplate('ajax_upload_list.html'));
                    exit();
                }
                if (isset($_GET['show_thumbnail']) && $_GET['show_thumbnail'] === 'yes') {
                    $show_thumbnail = true;
                    $image_library_list = array(0 => 'GD', 1 => 'Imagick');
                    //Test Thumbnail
                    if (isset($_GET['upload']) && isset($_FILES['image']['name'])) {
                        if (!empty($_FILES['image']['name'])) {
                            if (is_array($_FILES['image']['name'])) {
                                $_FILES['image']['name'] = array_sanitize($_FILES['image']['name']);
                            } else {
                                $_FILES['image']['name'] = input_filter($_FILES['image']['name']);
                            }
                            $upload_img = new Thumbnail(true);
                            $upload_img->setImageLibrary($image_library_list[$upload['current_library']]);
                            $upload_img->setThumb('data/temp/', $upload['thumbnail_height'], $upload['thumbnail_width']);
                            $upload_img->setRandom(true);
                            $upload_result = $upload_img->getImageUpload($_FILES, 'data/attachment/image/', true);
                            $data['result'] = $upload_result;
                            exit(json_encode($data));
                        }
                    }
                }
                break;
            case ($_GET['mod'] === 'social'):
                $content['social'] = 'active';
                $display = 'config_social';
                //Login with social account
                $socialConfig = SocialAccount::getInstance();
                $socialConfig->getConnection($conn);
                //Check social login
                if ($socialConfig->socialEnable() === true) {
                    $social_login['set'] = true;
                } else {
                    $social_login['set'] = false;
                }
                $social['current_type'] = $socialConfig->socialLoginType();
                $social_type_list = array('github');
                //Get Social Type
                if (isset($_POST['show_social_type'])) {
                    $social['type'] = $_POST['show_social_type'];
                    $social['type'] .= '_login';
                    switch ($social['type']) {
                        case 'github_login' === $social['type']:
                            $social_login['option'] = $socialConfig->githubConfig();
                            $social_login['type'] = $socialConfig->socialLoginType();
                            if ($social_login['type'] !== false) {
                                switch ($social_login['type']) {
                                    case 'github_login' === $social_login['type']:
                                        if ($social_login['option'] === false) {
                                            $social_login['option'] = array(
                                                'client_id' => '',
                                                'client_secret' => '',
                                                'redirect_url' => ''
                                            );
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
                            break;
                        default:
                            break;
                    }
                    include($template->loadTemplate('ajax_social_list.html'));
                    exit();
                }
                break;
            case ($_GET['mod'] === 'email'):
                $load->loadClass('email_config');
                $content['email'] = 'active';
                $display = 'config_email';
                //Get email config
                $emailConfig = EmailConfig::getInstance();
                $emailConfig->getConnection($conn);
                //Check social login
                if ($emailConfig->checkEmailEnable() === true) {
                    $email['set'] = true;
                } else {
                    $email['set'] = false;
                }
                $email['current_type'] = $emailConfig->checkEmailType();
                $email_type_list = array('localhost', 'smtp');
                //Get Email Type
                if (isset($_POST['show_email_type'])) {
                    $email['type'] = $_POST['show_email_type'];
                    switch ($email['type']) {
                        case 'localhost' === $email['type']:
                            if ($emailConfig->localhostEmailConfig() !== false) {
                                $localhostEmail = $emailConfig->localhostEmailConfig();
                                $email_option = array(
                                    'charset' => $localhostEmail['charset'],
                                    'send_from' => $localhostEmail['send_from'],
                                    'send_name' => $localhostEmail['send_name']
                                );
                            }
                            break;
                        case 'smtp' === $email['type']:
                            if ($emailConfig->smtpEmailConfig() !== false) {
                                $smtpEmail = $emailConfig->smtpEmailConfig();
                                $email_option = array(
                                    'smtp_host' => $smtpEmail['smtp_host'],
                                    'smtp_user' => $smtpEmail['smtp_user'],
                                    'smtp_pw' => $smtpEmail['smtp_pw'],
                                    'charset' => $smtpEmail['charset'],
                                    'smtp_send_name' => $smtpEmail['send_name']
                                );
                            }
                            break;
                        default:
                            break;
                    }
                    include($template->loadTemplate('ajax_email_list.html'));
                    exit();
                }
                //Check send test email
                if (isset($_GET['test']) && $email['set'] === true) {
                    switch ($_GET['test']) {
                        case 'test_email' === $_GET['test']:
                            if (isset($_POST['send_test'])) {
                                if (checkEmailValid($_POST['send_test']) === 1) {
                                    $load->loadClass('email_template');
                                    //Get email template
                                    $emailTemplate = EmailTemplate::getInstance();
                                    $email_tpl_option = array('template_dir' => 'template/email/');
                                    $emailTemplate->setOption($email_tpl_option);
                                    $test_msg['to'] = $_POST['send_test'];
                                    $test_msg['subject'] = $meta['name'];
                                    $mailParam = array(
                                        'test_success' => $LANG['admin-email']['test_success'],
                                        'web_name' => $meta['name']
                                    );
                                    $mailContent = $emailTemplate->loadEmailTemplate('test_email.html', $mailParam);
                                    require dirname(__FILE__).'/source/mailer/Exception.php';
                                    require dirname(__FILE__).'/source/mailer/PHPMailer.php';
                                    require dirname(__FILE__).'/source/mailer/SMTP.php';
                                    if ($email['current_type'] !== false) {
                                        //Instantiation and passing 'true' enables exceptions
                                        $mail = new PHPMailer(true);
                                        $mail->setLanguage(strtok($SYSTEM['system_lang'], '_'), 'source/mailer/language/');
                                        switch ($email['current_type']) {
                                            case 'localhost' === $email['current_type']:
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
                                                        $mail->addAddress($test_msg['to'], $login['username']);
                                                        $mail->isHTML(true);
                                                        $mail->Subject = $test_msg['subject'];
                                                        $mail->Body = $mailContent;
                                                        $mail->AltBody = $mailContent;
                                                        $mail->send();
                                                        if (!$mail->ErrorInfo) {
                                                            $display = 'email_success';
                                                        } else {
                                                            $display = 'email_error';
                                                        }
                                                    } catch (Exception $e) {
                                                        $display = 'email_error';
                                                    }
                                                }
                                                break;
                                            case 'smtp' === $email['current_type']:
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
                                                        $mail->addAddress($test_msg['to'], $login['username']);
                                                        //Set email format to HTML
                                                        $mail->isHTML(true);
                                                        $mail->Subject = $test_msg['subject'];
                                                        $mail->Body = $mailContent;
                                                        $mail->AltBody = $mailContent;
                                                        $mail->send();
                                                        if (!$mail->ErrorInfo) {
                                                            $display = 'email_success';
                                                        } else {
                                                            $display = 'email_error';
                                                        }
                                                    } catch (Exception $e) {
                                                        $display = 'email_error';
                                                    }
                                                }
                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                } else {
                                    $emailValid = false;
                                    $display = 'email_error';
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
                break;
            case ($_GET['mod'] === 'seo'):
                $load->loadClass('sitemap');
                $content['seo'] = 'active';
                $display = 'config_seo';
                //Get SEO Type
                if (isset($_POST['show_seo_type'])) {
                    $seo['type'] = $_POST['show_seo_type'];
                    switch ($seo['type']) {
                        case 'sitemap' === $seo['type']:
                            $sitemapConfig = DataRead::getInstance();
                            $sitemapConfig->getConnection($conn);
                            $read_query = array(
                                'config' => 'SELECT enable,auto_update FROM seo_sitemap_config WHERE id = ?'
                            );
                            $seo['option'] = $sitemapConfig->getConfig($read_query);
                            $read_query = array(
                                'config' => 'SELECT sitemap_path FROM seo_sitemap_config WHERE id = ?'
                            );
                            $seo['sitemap'] = $sitemapConfig->getConfig($read_query);
                            $sitemap['enable'] = array('1' => 'enable', '0' => 'disable');
                            $sitemap['auto_update'] = array('1' => 'enable', '0' => 'disable');
                            if (isset($_POST['generate_sitemap'])) {
                                if ($_POST['generate_sitemap'] == true) {
                                    $home_page[] = array('loc' => $base_url.'/', 'lastmod' => time(), 'changefreq' => 'always', 'priority' => '1.0');
                                    $sitemapArray = array(
                                        $home_page,
                                        $sitemapConfig->getSitemapArray('category', 'cid', $base_url),
                                        $sitemapConfig->getSitemapArray('board', 'bid', $base_url),
                                        $sitemapConfig->getSitemapArray('article', 'aid', $base_url)
                                    );
                                    if (isset($_POST['get_sitemap_path'])) {
                                        $sitemap['path'] = input_filter($_POST['get_sitemap_path']);
                                    } else {
                                        $sitemap['path'] = $seo['sitemap']['sitemap_path'];
                                    }
                                    $sitemap['timezone'] = $SYSTEM['system_timezone'];
                                    if (generateSitemap($sitemapArray, $sitemap) === true) {
                                        echo 1;
                                        exit();
                                    }
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    include($template->loadTemplate('ajax_seo_list.html'));
                    exit();
                }
                break;
            case ($_GET['mod'] === 'captcha'):
                $content['captcha'] = 'active';
                $display = 'config_captcha';
                require dirname(__FILE__).'/source/class/class_captcha_config.php';
                //Captcha Config
                $captchaConfig = CaptchaConfig::getInstance();
                $captchaConfig->getConnection($conn);
                if ($captchaConfig->checkCaptchaEnable() === true) {
                    $captcha['set'] = true;
                } else {
                    $captcha['set'] = false;
                }
                $captcha['current_type'] = $captchaConfig->checkCaptchaType();
                $captcha_type_list = array('simple_captcha', 'google_recaptcha', 'svg_captcha');
                $captcha['current_apply'] = unserialize($captchaConfig->checkCaptchaApply());
                if (is_array($captcha['current_apply']) === false) {
                    $captcha['current_apply'] = array('');
                }
                $captcha_apply_list = array('login', 'signup');
                //Get Captcha Type
                if (isset($_POST['show_captcha_type'])) {
                    $captcha['type'] = $_POST['show_captcha_type'];
                    switch ($captcha['type']) {
                        case 'simple_captcha' === $captcha['type']:
                            if ($captchaConfig->simpleCaptchaConfig() !== false) {
                                $simpleCaptcha = $captchaConfig->simpleCaptchaConfig();
                                $captcha_option = array(
                                    'image_height' => $simpleCaptcha['image_height'],
                                    'image_width' => $simpleCaptcha['image_width'],
                                    'font' => $simpleCaptcha['font_file'],
                                    'text_color' => $simpleCaptcha['text_color'],
                                    'noise_color' => $simpleCaptcha['noise_color'],
                                    'total_character' => $simpleCaptcha['total_character'],
                                    'random_dots' => $simpleCaptcha['random_dots'],
                                    'random_lines' => $simpleCaptcha['random_lines']
                                );
                                $check_sensitive = array(
                                    '0' => 'disable',
                                    '1' => 'enable'
                                );
                            }
                            break;
                        case 'google_recaptcha' === $captcha['type']:
                            require dirname(__FILE__).'/source/recaptcha/recaptcha.php';
                            $reCaptcha = $captchaConfig->reCaptchaConfig();
                            break;
                        case 'svg_captcha' === $captcha['type']:
                            require dirname(__FILE__).'/source/svg_captcha/SVGCaptcha.php';
                            if ($captchaConfig->svgCaptchaConfig() !== false) {
                                $svgCaptcha = $captchaConfig->svgCaptchaConfig();
                                $captcha_option = array(
                                    'image_height' => $svgCaptcha['image_height'],
                                    'image_width' => $svgCaptcha['image_width'],
                                    'total_character' => $svgCaptcha['total_character']
                                );
                                $svg_difficulty = array(
                                    '0' => 'easy',
                                    '1' => 'medium',
                                    '2' => 'hard'
                                );
                            }
                            break;
                        default:
                            break;
                    }
                    include($template->loadTemplate('ajax_captcha_list.html'));
                    exit();
                }
                break;
            case ($_GET['mod'] === 'database'):
                $content['database'] = 'active';
                if (isset($_GET['check'])) {
                    if (checkReferer() !== true) exit('Access Denied');
                    if ($_GET['check'] === 'db_size') {
                        echo checkDatabaseSize($conn, DB_NAME);
                        exit();
                    }
                } else {
                    $display = 'config_database';
                }
                break;
            default:
                $content['global'] = 'active';
                $display = 'view_global';
                $adminConfig = DataRead::getInstance();
                $adminConfig->getConnection($conn);
                $read_query = array(
                    'config' => 'SELECT web_name,web_description,web_language FROM global_config WHERE id = ?'
                );
                $globalConfig = $adminConfig->getConfig($read_query);
                break;
        }
    } else {
        $content['global'] = 'active';
        $display = 'view_global';
        $adminConfig = DataRead::getInstance();
        $adminConfig->getConnection($conn);
        $read_query = array(
            'config' => 'SELECT web_name,web_description,web_language FROM global_config WHERE id = ?'
        );
        $globalConfig = $adminConfig->getConfig($read_query);
    }
} else {
    $show_admin = false;
    header('HTTP/1.0 403 Forbidden');
    $display = 'view_denied';
}

//Check change setting
if (isset($_GET['update'])) {
    switch ($_GET['update']) {
        case ($_GET['update'] === 'global_setting'):
            $back['mod'] = 'global';
            if (isset($_POST['web_name']) && isset($_POST['web_description']) && isset($_POST['web_lang'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                $update_query = array(
                    'config' => 'UPDATE global_config SET web_name = ?, web_description = ?, web_language = ? WHERE id = ?', 
                    'prepare' => 'sssi', 
                    'bind' => array_sanitize(array($_POST['web_name'], $_POST['web_description'], $_POST['web_lang'], 1))
                );
                $update['config']->updateConfig($update_query);
                sleep(1);
                $display = 'view_success';
            }
            break;
        case ($_GET['update'] === 'member'):
            $back['mod'] = 'member';
            if (isset($_POST['update_id']) || isset($_POST['update_multi_id'])) {
                $update['data'] = DataUpdate::getInstance();
                $update['data']->getConnection($conn);
                if (isset($_POST['update_array'])) {
                    if (isset($_POST['update_id'])) {
                        $update['data']->updateUser($_POST['update_id'], array_sanitize($_POST['update_array']));
                    } elseif (isset($_POST['update_multi_id']) && is_array($_POST['update_multi_id'])) {
                        if (($key = array_search(1, $_POST['update_multi_id'])) !== false) {
                            unset($_POST['update_multi_id'][$key]);
                        }
                        foreach ($_POST['update_multi_id'] as $key => $value) {
                            $update['data']->updateMultiUser($value, $_POST['update_array']);
                        }
                    }
                }
                $memberConfig = Admin::getInstance();
                $memberConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $members = $memberConfig->searchData('member', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $members = $memberConfig->countData('member');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=member&amp;page=(:num)';
                $paginator = new Pagination($members, $itemsPerPage, $currentPage, $urlPattern);
                $member_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_member = $paginator->getTotalItems();
                include($template->loadTemplate('ajax_member_list.html'));
                exit();
            }
            break;
        case ($_GET['update'] === 'article'):
            $back['mod'] = 'article';
            $articleProperty = array('0' => 'article', '3' => 'pinned');
            if (isset($_POST['update_multi_id'])) {
                $update['data'] = DataUpdate::getInstance();
                $update['data']->getConnection($conn);
                if (isset($_POST['update_array'])) {
                    if (isset($_POST['update_multi_id']) && is_array($_POST['update_multi_id'])) {
                        foreach ($_POST['update_multi_id'] as $key => $value) {
                            $update['data']->updateArticleBoard($value, $_POST['update_array']);
                        }
                    }
                }
                $articleConfig = Admin::getInstance();
                $articleConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $articles = $articleConfig->searchData('article', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $articles = $articleConfig->countData('article');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=article&amp;page=(:num)';
                $paginator = new Pagination($articles, $itemsPerPage, $currentPage, $urlPattern);
                $article_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_article = $paginator->getTotalItems();
                foreach ($article_result as $key => $value) {
                    $article_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                }
                include($template->loadTemplate('ajax_article_list.html'));
                exit();
            }
            break;
        case ($_GET['update'] === 'board'):
            $back['mod'] = 'board';
            if (isset($_POST['update_id']) || isset($_POST['update_multi_id'])) {
                $update['data'] = DataUpdate::getInstance();
                $update['data']->getConnection($conn);
                if (isset($_POST['update_array'])) {
                    if (isset($_POST['update_id'])) {
                        $update['data']->updateBoard($_POST['update_id'], array_sanitize($_POST['update_array']));
                    } elseif (isset($_POST['update_multi_id']) && is_array($_POST['update_multi_id'])) {
                        foreach ($_POST['update_multi_id'] as $key => $value) {
                            $update['data']->updateBoardCategory($value, $_POST['update_array']);
                        }
                    }
                }
                $boardConfig = Admin::getInstance();
                $boardConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $boards = $boardConfig->searchData('board', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $boards = $boardConfig->countData('board');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=board&amp;page=(:num)';
                $paginator = new Pagination($boards, $itemsPerPage, $currentPage, $urlPattern);
                $board_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_board = $paginator->getTotalItems();
                foreach ($board_result as $key => $value) {
                    $board_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $board_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                include($template->loadTemplate('ajax_board_list.html'));
                exit();
            }
            break;
        case ($_GET['update'] === 'category'):
            $back['mod'] = 'category';
            if (isset($_POST['update_id']) || isset($_POST['update_multi_id'])) {
                $update['data'] = DataUpdate::getInstance();
                $update['data']->getConnection($conn);
                if (isset($_POST['update_array'])) {
                    if (isset($_POST['update_id'])) {
                        $update['data']->updateCategory($_POST['update_id'], array_sanitize($_POST['update_array']));
                    } elseif (isset($_POST['update_multi_id']) && is_array($_POST['update_multi_id'])) {
                        foreach ($_POST['update_multi_id'] as $key => $value) {
                            $update['data']->updateCategorySitemap($value, $_POST['update_array']);
                        }
                    }
                }
                $categoryConfig = Admin::getInstance();
                $categoryConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $categorys = $categoryConfig->searchData('category', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $categorys = $categoryConfig->countData('category');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=category&amp;page=(:num)';
                $paginator = new Pagination($categorys, $itemsPerPage, $currentPage, $urlPattern);
                $category_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_category = $paginator->getTotalItems();
                foreach ($category_result as $key => $value) {
                    $category_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $category_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                include($template->loadTemplate('ajax_category_list.html'));
                exit();
            }
            break;
        case ($_GET['update'] === 'upload_enable'):
            $back['mod'] = 'upload';
            if (isset($_POST['upload_set'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['upload_set']) {
                    case 'enable' === $_POST['upload_set']:
                        if (isset($_POST['change_type'])) {
                            $get_type = checkTypeChange($_POST['change_type'], array('local', 'remote'));
                            if (isset($_POST['change_library'])) {
                                $update_query = array(
                                    'config' => 'UPDATE upload_config SET enable = ?, type = ?, image_library = ? WHERE id = ?', 
                                    'prepare' => 'isii', 
                                    'bind' => array(1, $get_type, $_POST['change_library'], 1)
                                );
                            } else {
                                $update_query = array(
                                    'config' => 'UPDATE upload_config SET enable = ?, type = ? WHERE id = ?', 
                                    'prepare' => 'isi', 
                                    'bind' => array(1, $get_type, 1)
                                );
                            }
                        } else {
                            $update_query = array(
                                'config' => 'UPDATE upload_config SET enable = ? WHERE id = ?', 
                                'prepare' => 'ii', 
                                'bind' => array(1, 1)
                            ); 
                        }
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'disable' === $_POST['upload_set']:
                        $update_query = array(
                            'config' => 'UPDATE upload_config SET enable = ? WHERE id = ?', 
                            'prepare' => 'ii', 
                            'bind' => array(0, 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'upload_setting'):
            $back['mod'] = 'upload';
            if (isset($_POST['upload_type']) && !isset($_POST['ftp_test'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['upload_type']) {
                    case 'local' === $_POST['upload_type']:
                        $update_query = array(
                            'config' => 'UPDATE upload_local SET local_dir = ?, local_url = ?, allowed_ext = ?, disallowed_ext = ?, max_size = ? WHERE id = ?', 
                            'prepare' => 'ssssii', 
                            'bind' => array_sanitize(array($_POST['local_dir'], $_POST['local_url'], $_POST['allowed_ext'], $_POST['disallowed_ext'], $_POST['max_size'], 1))
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE upload_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, 'local', 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'remote' === $_POST['upload_type']:
                        $_POST['ftp_host'] = str_replace('ftp://', '', $_POST['ftp_host']);
                        $_POST['remote_dir'] = rtrim($_POST['remote_dir'], '/\\');
                        $_POST['remote_url'] = rtrim($_POST['remote_url'], '/\\');
                        $update_query = array(
                            'config' => 'UPDATE upload_remote SET 
                                        use_ssl = ?,
                                        ftp_host = ?,
                                        ftp_port = ?,
                                        ftp_user = ?,
                                        ftp_pw = ?,
                                        pasv = ?,
                                        remote_dir = ?,
                                        remote_url = ?,
                                        ftp_timeout = ?,
                                        allowed_ext = ?,
                                        disallowed_ext = ?,
                                        max_size = ? WHERE id = ?', 
                            'prepare' => 'isissississii', 
                            'bind' => array_sanitize(array($_POST['use_ssl'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pw'], $_POST['pasv'], $_POST['remote_dir'], $_POST['remote_url'], $_POST['ftp_timeout'], $_POST['allowed_ext'], $_POST['disallowed_ext'], $_POST['max_size'], 1))
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE upload_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, 'remote', 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            } elseif (isset($_POST['ftp_test'])) {
                require dirname(__FILE__).'/source/ftp/FTPClient.php';
                require dirname(__FILE__).'/source/ftp/FTPException.php';
                require dirname(__FILE__).'/source/ftp/FTPWrapper.php';
                $_POST['ftp_host'] = str_replace(array('https://', 'http://', 'ftp://'), '', $_POST['ftp_host']);
                $ftp_host = $_POST['ftp_host'];
                $ftp_login = $_POST['ftp_user'];
                $use_ssl = (isset($_POST['use_ssl']) && $_POST['use_ssl'] === 1) ? true : false;
                $ftp_port = (isset($_POST['ftp_port'])) ? $_POST['ftp_port'] : 21;
                $ftp_password = $_POST['ftp_pw'];
                $content = generatePassword(16);
                if (file_exists(dirname(__FILE__).'/data/temp/image/') === false) {
                    mkdir(dirname(__FILE__).'/data/temp/image/');
                    if (file_exists(dirname(__FILE__).'/data/temp/image/testFile/') === false) {
                        mkdir(dirname(__FILE__).'/data/temp/image/testFile/');
                    }
                }
                $fp = fopen(dirname(__FILE__).'/data/temp/image/testFile/test.html', 'wb');
                fwrite($fp, $content);
                fclose($fp);
                $source_directory = dirname(__FILE__).'/data/temp/image/';
                $target_directory = $_POST['remote_dir'].'/test';
                $ftp = new \FTPClient\FTPClient();
                $ftp->connectFTP($ftp_host, $use_ssl, $ftp_port);
                $ftp->loginFTP($ftp_login, $ftp_password);
                $ftp->pasv(true);
                $ftp->mkdir(rtrim($_POST['remote_dir'], '/\\').'/test', false);
                $ftp->putAll($source_directory, $target_directory);
                $filePath = rtrim($_POST['remote_dir'], '/\\').'/test/testFile';
                $ftp->getAll($filePath, dirname(__FILE__).'/data/temp');
                $checkFile = fopen(dirname(__FILE__).'/data/temp/test.html', 'r');
                if ($checkFile !== false) {
                    $checkCode = fgets($checkFile);
                    if ($checkCode == $content) {
                        $test['type'] = 'upload';
                        exit('true');
                    }
                    fclose($checkFile);
                } else {
                    exit('false');
                }
            } else {
                if (isset($_POST['image_library'])) {
                    $update['config'] = DataUpdate::getInstance();
                    $update['config']->getConnection($conn);
                    $getHeight = (isset($_POST['thumbnail_height'])) ? $_POST['thumbnail_height'] : 0;
                    $getWidth = (isset($_POST['thumbnail_width'])) ? $_POST['thumbnail_width'] : 200;
                    $update_query = array(
                        'config' => 'UPDATE upload_config SET image_library = ?, thumbnail_height = ?, thumbnail_width = ? WHERE id = ?', 
                        'prepare' => 'iiii', 
                        'bind' => array($_POST['image_library'], $getHeight, $getWidth, 1)
                    );
                    $update['config']->updateConfig($update_query);
                    sleep(1);
                    $display = 'view_success';
                }
            }
            break;
        case ($_GET['update'] === 'email_enable'):
            $back['mod'] = 'email';
            if (isset($_POST['email_set'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['email_set']) {
                    case 'enable' === $_POST['email_set']:
                        if (isset($_POST['change_type'])) {
                            $get_type = checkTypeChange($_POST['change_type'], array('localhost', 'smtp'));
                            $update_query = array(
                                'config' => 'UPDATE email_config SET enable = ?, type = ? WHERE id = ?', 
                                'prepare' => 'isi', 
                                'bind' => array(1, $get_type, 1)
                            );
                        } else {
                            $update_query = array(
                                'config' => 'UPDATE email_config SET enable = ? WHERE id = ?', 
                                'prepare' => 'ii', 
                                'bind' => array(1, 1)
                            ); 
                        }
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'disable' === $_POST['email_set']:
                        $update_query = array(
                            'config' => 'UPDATE email_config SET enable = ? WHERE id = ?', 
                            'prepare' => 'ii', 
                            'bind' => array(0, 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'email_setting'):
            $back['mod'] = 'email';
            if (isset($_POST['email_type'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['email_type']) {
                    case 'localhost' === $_POST['email_type']:
                        $update_query = array(
                            'config' => 'UPDATE email_localhost SET charset = ?, send_from = ?, send_name = ? WHERE id = ?', 
                            'prepare' => 'sssi', 
                            'bind' => array_sanitize(array($_POST['charset'], $_POST['send_from'], $_POST['send_name'], 1))
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE email_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, 'localhost', 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'smtp' === $_POST['email_type']:
                        $update_query = array(
                            'config' => 'UPDATE email_smtp SET smtp_host = ?, smtp_user = ?, smtp_pw = ?, charset = ?, send_name = ? WHERE id = ?', 
                            'prepare' => 'sssssi', 
                            'bind' => array_sanitize(array($_POST['smtp_host'], $_POST['smtp_user'], $_POST['smtp_pw'], $_POST['charset'], $_POST['smtp_send_name'], 1))
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE email_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, 'smtp', 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'seo_setting'):
            $back['mod'] = 'seo';
            if (isset($_POST['seo_type'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['seo_type']) {
                    case 'sitemap' === $_POST['seo_type']:
                        $update_query = array(
                            'config' => 'UPDATE seo_sitemap_config SET enable = ?, auto_update = ?, sitemap_path = ? WHERE id = ?', 
                            'prepare' => 'iisi', 
                            'bind' => array_sanitize(array($_POST['sitemap_function_set'], $_POST['auto_update_set'], $_POST['sitemap_path'], 1))
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'captcha_enable'):
            $back['mod'] = 'captcha';
            if (isset($_POST['captcha_set'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                if (isset($_POST['apply_type'])) {
                    $get_apply = checkApplyChange($_POST['apply_type']);
                    $get_apply = serialize($get_apply);
                    $update_query = array(
                        'config' => 'UPDATE captcha_config SET apply = ? WHERE id = ?', 
                        'prepare' => 'si', 
                        'bind' => array($get_apply, 1)
                    );
                    $update['config']->updateConfig($update_query);
                }
                switch ($_POST['captcha_set']) {
                    case 'enable' === $_POST['captcha_set']:
                        if (isset($_POST['change_type'])) {
                            $get_type = checkTypeChange($_POST['change_type'], array('simple_captcha', 'google_recaptcha', 'svg_captcha'));
                            $update_query = array(
                                'config' => 'UPDATE captcha_config SET enable = ?, type = ? WHERE id = ?', 
                                'prepare' => 'isi', 
                                'bind' => array(1, $get_type, 1)
                            );
                        } else {
                            $update_query = array(
                                'config' => 'UPDATE captcha_config SET enable = ? WHERE id = ?', 
                                'prepare' => 'ii', 
                                'bind' => array(1, 1)
                            ); 
                        }
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'disable' === $_POST['captcha_set']:
                        $update_query = array(
                            'config' => 'UPDATE captcha_config SET enable = ? WHERE id = ?', 
                            'prepare' => 'ii', 
                            'bind' => array(0, 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'captcha_setting'):
            $back['mod'] = 'captcha';
            if (isset($_POST['captcha_type'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                //Detect captcha type
                switch ($_POST['captcha_type']) {
                    case 'simple_captcha' === $_POST['captcha_type']:
                        $update_query = array(
                            'config' => 'UPDATE simple_captcha SET 
                                image_height = ?, 
                                image_width = ?, 
                                font_file = ?, 
                                text_color = ?, 
                                noise_color = ?, 
                                total_character = ?, 
                                random_dots = ?, 
                                random_lines = ?, 
                                check_sensitive = ? WHERE id = ?', 
                            //Get update array
                            'prepare' => 'iisssiiiii', 
                            'bind' => array(
                                input_filter($_POST['image_height']), 
                                input_filter($_POST['image_width']), 
                                input_filter($_POST['font']), 
                                input_filter($_POST['text_color']), 
                                input_filter($_POST['noise_color']), 
                                input_filter($_POST['total_character']), 
                                input_filter($_POST['random_dots']), 
                                input_filter($_POST['random_lines']), 
                                input_filter($_POST['sensitive_set']), 1)
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE captcha_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, $_POST['captcha_type'], 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'google_recaptcha' === $_POST['captcha_type']:
                        $update_query = array(
                            'config' => 'UPDATE google_recaptcha SET site_key = ?, secret_key = ? WHERE id = ?', 
                            'prepare' => 'ssi', 
                            'bind' => array(input_filter($_POST['site_key']), input_filter($_POST['secret_key']), 1)
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE captcha_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, $_POST['captcha_type'], 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'svg_captcha' === $_POST['captcha_type']:
                        $update_query = array(
                            'config' => 'UPDATE svg_captcha SET image_height = ?, image_width = ?, total_character = ?, difficulty = ? WHERE id = ?', 
                            'prepare' => 'iiiii', 
                            'bind' => array_sanitize(array($_POST['image_height'], $_POST['image_width'], $_POST['total_character'], $_POST['difficulty_set'], 1))
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE captcha_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, $_POST['captcha_type'], 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'social_enable'):
            $back['mod'] = 'social';
            if (isset($_POST['social_login_set'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['social_login_set']) {
                    case 'enable' === $_POST['social_login_set']:
                        if (isset($_POST['change_type'])) {
                            $get_type = checkTypeChange($_POST['change_type'], array('github_login'));
                            $update_query = array(
                                'config' => 'UPDATE social_login_config SET enable = ?, type = ? WHERE id = ?', 
                                'prepare' => 'isi', 
                                'bind' => array(1, $get_type, 1)
                            );
                        } else {
                            $update_query = array(
                                'config' => 'UPDATE social_login_config SET enable = ? WHERE id = ?', 
                                'prepare' => 'ii', 
                                'bind' => array(1, 1)
                            ); 
                        }
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    case 'disable' === $_POST['social_login_set']:
                        $update_query = array(
                            'config' => 'UPDATE social_login_config SET enable = ? WHERE id = ?', 
                            'prepare' => 'ii', 
                            'bind' => array(0, 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
                        break;
                    default:
                        break;
                }
            }
            break;
        case ($_GET['update'] === 'social_setting'):
            $back['mod'] = 'social';
            if (isset($_POST['social_type'])) {
                $update['config'] = DataUpdate::getInstance();
                $update['config']->getConnection($conn);
                switch ($_POST['social_type']) {
                    case 'github_login' === $_POST['social_type']:
                        $update_query = array(
                            'config' => 'UPDATE github_login SET client_id = ?, client_secret = ?, redirect_url = ? WHERE id = ?', 
                            'prepare' => 'sssi', 
                            'bind' => array(input_filter($_POST['client_id']), input_filter($_POST['client_secret']), $_POST['redirect_url'], 1)
                        );
                        $update['config']->updateConfig($update_query);
                        $update_query = array(
                            'config' => 'UPDATE social_login_config SET enable = ?, type = ? WHERE id = ?', 
                            'prepare' => 'isi', 
                            'bind' => array(1, $_POST['social_type'], 1)
                        );
                        $update['config']->updateConfig($update_query);
                        sleep(1);
                        $display = 'view_success';
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

//Check delete data
if (isset($_GET['delete'])) {
    switch ($_GET['delete']) {
        case ($_GET['delete'] === 'member'):
            if (isset($_POST['delete_id']) || isset($_POST['delete_multi_id']) || isset($_POST['delete'])) {
                $delete['data'] = DataDelete::getInstance();
                $delete['data']->getConnection($conn);
                if (isset($_POST['delete_id']) && $_POST['delete_id'] != $login['uid']) {
                    $delete['data']->deleteUser($_POST['delete_id'], $login['uid']);
                } elseif (isset($_POST['delete_multi_id']) && is_array($_POST['delete_multi_id'])) {
                    $countDelete = count($_POST['delete_multi_id']);
                    if (($key = array_search(1, $_POST['delete_multi_id'])) !== false) {
                        unset($_POST['delete_multi_id'][$key]);
                    }
                    foreach ($_POST['delete_multi_id'] as $key => $value) {
                        $delete['data']->deleteUser($value, $login['uid']);
                    }
                }
                $memberConfig = Admin::getInstance();
                $memberConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $members = $memberConfig->searchData('member', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $members = $memberConfig->countData('member');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $lastPage = (!empty($_POST['last_page'])) ? input_filter(checkPage($_POST['last_page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=member&amp;page=(:num)';
                //Count rows of last page
                $countLast = countLastPageResult($members, $currentPage, $itemsPerPage);
                if (isset($countDelete) && $countLast == 0 && $currentPage >= $lastPage) {
                    $currentPage = $currentPage - 1;
                }
                $paginator = new Pagination($members, $itemsPerPage, $currentPage, $urlPattern);
                $member_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_member = $paginator->getTotalItems();
                include($template->loadTemplate('ajax_member_list.html'));
                exit();
            }
            break;
        case ($_GET['delete'] === 'article'):
            $articleProperty = array('0' => 'article', '3' => 'pinned');
            if (isset($_POST['delete_id']) || isset($_POST['delete_multi_id']) || isset($_POST['delete'])) {
                $delete['data'] = DataDelete::getInstance();
                $delete['data']->getConnection($conn);
                if (isset($_POST['delete_id'])) {
                    $delete['data']->deleteArticle($_POST['delete_id']);
                } elseif (isset($_POST['delete_multi_id']) && is_array($_POST['delete_multi_id'])) {
                    $countDelete = count($_POST['delete_multi_id']);
                    foreach ($_POST['delete_multi_id'] as $key => $value) {
                        $delete['data']->deleteArticle($value);
                    }
                }
                $articleConfig = Admin::getInstance();
                $articleConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $articles = $articleConfig->searchData('article', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $articles = $articleConfig->countData('article');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $lastPage = (!empty($_POST['last_page'])) ? input_filter(checkPage($_POST['last_page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=article&amp;page=(:num)';
                //Count rows of last page
                $countLast = countLastPageResult($articles, $currentPage, $itemsPerPage);
                if (isset($countDelete) && $countLast == 0 && $currentPage >= $lastPage) {
                    $currentPage = $currentPage - 1;
                }
                $paginator = new Pagination($articles, $itemsPerPage, $currentPage, $urlPattern);
                $article_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_article = $paginator->getTotalItems();
                foreach ($article_result as $key => $value) {
                    $article_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                }
                include($template->loadTemplate('ajax_article_list.html'));
                exit();
            }
            break;
        case ($_GET['delete'] === 'board'):
            if (isset($_POST['delete_id']) || isset($_POST['delete_multi_id']) || isset($_POST['delete'])) {
                $delete['data'] = DataDelete::getInstance();
                $delete['data']->getConnection($conn);
                if (isset($_POST['delete_id'])) {
                    $delete['data']->deleteBoard($_POST['delete_id']);
                } elseif (isset($_POST['delete_multi_id']) && is_array($_POST['delete_multi_id'])) {
                    $countDelete = count($_POST['delete_multi_id']);
                    foreach ($_POST['delete_multi_id'] as $key => $value) {
                        $delete['data']->deleteBoard($value);
                    }
                }
                $boardConfig = Admin::getInstance();
                $boardConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $boards = $boardConfig->searchData('board', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $boards = $boardConfig->countData('board');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $lastPage = (!empty($_POST['last_page'])) ? input_filter(checkPage($_POST['last_page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=board&amp;page=(:num)';
                //Count rows of last page
                $countLast = countLastPageResult($boards, $currentPage, $itemsPerPage);
                if (isset($countDelete) && $countLast == 0 && $currentPage >= $lastPage) {
                    $currentPage = $currentPage - 1;
                }
                $paginator = new Pagination($boards, $itemsPerPage, $currentPage, $urlPattern);
                $board_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_board = $paginator->getTotalItems();
                foreach ($board_result as $key => $value) {
                    $board_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $board_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                include($template->loadTemplate('ajax_board_list.html'));
                exit();
            }
            break;
        case ($_GET['delete'] === 'category'):
            if (isset($_POST['delete_id']) || isset($_POST['delete_multi_id']) || isset($_POST['delete'])) {
                $delete['data'] = DataDelete::getInstance();
                $delete['data']->getConnection($conn);
                if (isset($_POST['delete_id'])) {
                    $delete['data']->deleteCategory($_POST['delete_id']);
                } elseif (isset($_POST['delete_multi_id']) && is_array($_POST['delete_multi_id'])) {
                    $countDelete = count($_POST['delete_multi_id']);
                    foreach ($_POST['delete_multi_id'] as $key => $value) {
                        $delete['data']->deleteCategory($value);
                    }
                }
                $categoryConfig = Admin::getInstance();
                $categoryConfig->getConnection($conn);
                if (isset($_POST['keyword']) && isset($_POST['sortBy'])) {
                    $categorys = $categoryConfig->searchData('category', $_POST['keyword'], $_POST['sortBy']);
                } else {
                    $categorys = $categoryConfig->countData('category');
                }
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $lastPage = (!empty($_POST['last_page'])) ? input_filter(checkPage($_POST['last_page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=category&amp;page=(:num)';
                //Count rows of last page
                $countLast = countLastPageResult($categorys, $currentPage, $itemsPerPage);
                if (isset($countDelete) && $countLast == 0 && $currentPage >= $lastPage) {
                    $currentPage = $currentPage - 1;
                }
                $paginator = new Pagination($categorys, $itemsPerPage, $currentPage, $urlPattern);
                $category_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_category = $paginator->getTotalItems();
                foreach ($category_result as $key => $value) {
                    $category_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $category_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                include($template->loadTemplate('ajax_category_list.html'));
                exit();
            }
            break;
        default:
            break;
    }
}

//Check create data
if (isset($_GET['create'])) {
    switch ($_GET['create']) {
        case ($_GET['create'] === 'member'):
            $back['mod'] = 'member';
            if (isset($_POST['create_array']) && is_array($_POST['create_array'])) {
                $create['data'] = DataCreate::getInstance();
                $create['data']->getConnection($conn);
                foreach ($_POST['create_array'] as $key => $value) {
                    $create_array[$key] = ($value != '') ? input_filter($value) : '';
                }
                $checkData = Check::getInstance();
                $checkData->getConnection($conn);
                if ($checkData->checkUsername($create_array['username']) === false) {
                    $error['type'] = 'duplicate_username';
                    include($template->loadTemplate('data_add_error.html'));
                    exit();
                } elseif ($checkData->checkEmail($create_array['email']) === false) {
                    $error['type'] = 'duplicate_email';
                    include($template->loadTemplate('data_add_error.html'));
                    exit();
                } elseif (checkEmpty($create_array, array('display_name', 'username', 'email')) === false) {
                    $error['type'] = 'input_empty';
                    include($template->loadTemplate('data_add_error.html'));
                    exit();
                } else {
                    $get_date = time();
                    $randomPassword = generatePassword(8, 'ABCDEFGHuiufbwhfbuy123456789!#$%&');
                    $create_array['password'] = password_hash($randomPassword, PASSWORD_DEFAULT);
                    $create_array['language'] = 'en_US';
                    $create_array['online_status'] = $get_date;
                    $create_array['last_login'] = $get_date;
                    $create_array['join_date'] = $get_date;
                    $create['data']->createUser($create_array);
                    $memberConfig = Admin::getInstance();
                    $memberConfig->getConnection($conn);
                    $members = $memberConfig->showData('member', 'DESC');
                    //Check page value
                    $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                    $itemsPerPage = 10;
                    $urlPattern = 'admin.php?mod=member&amp;page=(:num)';
                    $paginator = new Pagination($members, $itemsPerPage, $currentPage, $urlPattern);
                    $member_result = $paginator->getResults();
                    $total_pages = $paginator->getNumPages();
                    $total_member = $paginator->getTotalItems();
                    include($template->loadTemplate('ajax_member_list.html'));
                    exit();
                }
            }
            break;
        case ($_GET['create'] === 'board'):
            $back['mod'] = 'board';
            if (isset($_POST['create_array']) && is_array($_POST['create_array'])) {
                $create['data'] = DataCreate::getInstance();
                $create['data']->getConnection($conn);
                foreach ($_POST['create_array'] as $key => $value) {
                    $create_array[$key] = ($value != '') ? input_filter($value) : '' ;
                }
                if (checkEmpty($create_array, array('board_name', 'board_description')) === false) {
                    $error['type'] = 'input_empty';
                    include($template->loadTemplate('data_add_error.html'));
                    exit();
                }
                $get_date = time();
                $create_array['last_edit'] = $get_date;
                $create_array['create_date'] = $get_date;
                $create['data']->createBoard($create_array);
                $boardConfig = Admin::getInstance();
                $boardConfig->getConnection($conn);
                $boards = $boardConfig->showData('board', 'DESC');
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=board&amp;page=(:num)';
                $paginator = new Pagination($boards, $itemsPerPage, $currentPage, $urlPattern);
                $board_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_board = $paginator->getTotalItems();
                foreach ($board_result as $key => $value) {
                    $board_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $board_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                include($template->loadTemplate('ajax_board_list.html'));
                exit();
            }
            break;
        case ($_GET['create'] === 'category'):
            $back['mod'] = 'category';
            if (isset($_POST['create_array']) && is_array($_POST['create_array'])) {
                $create['data'] = DataCreate::getInstance();
                $create['data']->getConnection($conn);
                foreach ($_POST['create_array'] as $key => $value) {
                    $create_array[$key] = ($value != '') ? input_filter($value) : '' ;
                }
                if (checkEmpty($create_array, array('category_name', 'category_description')) === false) {
                    $error['type'] = 'input_empty';
                    include($template->loadTemplate('data_add_error.html'));
                    exit();
                }
                $get_date = time();
                $create_array['last_edit'] = $get_date;
                $create_array['create_date'] = $get_date;
                $create['data']->createCategory($create_array);
                $categoryConfig = Admin::getInstance();
                $categoryConfig->getConnection($conn);
                $categorys = $categoryConfig->showData('category');
                //Check page value
                $currentPage = (!empty($_POST['page'])) ? input_filter(checkPage($_POST['page'])) : 1;
                $itemsPerPage = 10;
                $urlPattern = 'admin.php?mod=category&amp;page=(:num)';
                $paginator = new Pagination($categorys, $itemsPerPage, $currentPage, $urlPattern);
                $category_result = $paginator->getResults();
                $total_pages = $paginator->getNumPages();
                $total_category = $paginator->getTotalItems();
                foreach ($category_result as $key => $value) {
                    $category_result[$key]['last_edit'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['last_edit']);
                    $category_result[$key]['create_date'] = getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $value['create_date'], 'Y-m-d');
                }
                include($template->loadTemplate('ajax_category_list.html'));
                exit();
            }
            break;
        default:
            break;
    }
}

include($template->loadTemplate('header_admin.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_admin.html'));
