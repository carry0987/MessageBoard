<?php
class Language
{
    private $lang_set;

    public function __construct($path)
    {
        $this->lang_set = '';
        $this->path = $path;
    }

    private function setCookie($lang)
    {
        setcookie('language', $lang, time()+86400, $this->path.'/', null, true, true);
    }

    public function getLinks($params = array())
    {
        $query_url = '';
        if (!empty($params) === true) {
            unset($params['lang']);
            $query_url = '?'.http_build_query($params);
        }
        return $query_url;
    }

    public function loadBrowserLanguage($browserLanguage)
    {
        switch ($browserLanguage) {
            case ($browserLanguage == 'en'):
                $this->lang_set = '/language/en_US.php';
                break;
            case ($browserLanguage == 'zh'):
                $this->lang_set = '/language/zh_TW.php';
                break;
            case ($browserLanguage == 'ja'):
                $this->lang_set = '/language/ja_JP.php';
                break;
            case ($browserLanguage == 'th'):
                $this->lang_set = '/language/th_TH.php';
                break;
            default:
                $this->lang_set = '/language/en_US.php';
                break;
        }
        return $this->lang_set;
    }

    public function loadCookieLanguage($cookieLang)
    {
        switch ($cookieLang) {
            case ($cookieLang == 'en_US'):
                $this->lang_set = '/language/en_US.php';
                break;
            case ($cookieLang == 'zh_TW'):
                $this->lang_set = '/language/zh_TW.php';
                break;
            case ($cookieLang == 'ja_JP'):
                $this->lang_set = '/language/ja_JP.php';
                break;
            case ($cookieLang == 'th_TH'):
                $this->lang_set = '/language/th_TH.php';
                break;
            default:
                $this->lang_set = '/language/en_US.php';
                break;
        }
        return $this->lang_set;
    }

    public function setLanguage($getLang)
    {
        switch ($getLang) {
            case ($getLang === 'zh_TW'):
                $this->setCookie('zh_TW');
                break;
            case ($getLang === 'en_US'):
                $this->setCookie('en_US');
                break;
            case ($getLang === 'ja_JP'):
                $this->setCookie('ja_JP');
                break;
            case ($getLang === 'th_TH'):
                $this->setCookie('th_TH');
                break;
            default:
                $this->setCookie('en_US');
                break;
        }
    }
}
