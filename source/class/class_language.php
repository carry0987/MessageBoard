<?php
class Language
{
    private $lang_set = array();
    private $path;
    private $lang_file_list;
    private $another_lang_file;
    private static $config_id = 1;

    public function __construct($path)
    {
        $this->path = $path;
    }

    private function setCookie($lang, $security)
    {
        setcookie('language', $lang, time()+86400, $this->path.'/', null, $security, true);
    }

    public function setLanguageFile($file_list)
    {
        $this->lang_file_list = $file_list;
    }

    public function addLanguageFile($file_list)
    {
        $this->lang_file_list = $file_list;
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

    public function loadLanguage($language)
    {
        switch ($language) {
            case ($language == 'en' || $language == 'en_US'):
                foreach ($this->lang_file_list as $value) {
                    $this->lang_set[$value] = '/language/en_US/en_US.'.$value.'.php';
                }
                $current_lang = 'en_US';
                break;
            case ($language == 'zh' || $language == 'zh_TW'):
                foreach ($this->lang_file_list as $value) {
                    $this->lang_set[$value] = '/language/zh_TW/zh_TW.'.$value.'.php';
                }
                $current_lang = 'zh_TW';
                break;
            case ($language == 'ja' || $language == 'ja_JP'):
                foreach ($this->lang_file_list as $value) {
                    $this->lang_set[$value] = '/language/ja_JP/ja_JP.'.$value.'.php';
                }
                $current_lang = 'ja_JP';
                break;
            case ($language == 'th' || $language == 'th_TH'):
                foreach ($this->lang_file_list as $value) {
                    $this->lang_set[$value] = '/language/th_TH/th_TH.'.$value.'.php';
                }
                $current_lang = 'th_TH';
                break;
            default:
                foreach ($this->lang_file_list as $value) {
                    $this->lang_set[$value] = '/language/en_US/en_US.'.$value.'.php';
                }
                $current_lang = 'en_US';
                break;
        }
        $this->current_lang = $current_lang;
        return $this->lang_set;
    }

    public function getCurrentLang()
    {
        $current_lang = $this->current_lang;
        return $current_lang;
    }

    public function setLanguage($getLang, $getSecurity)
    {
        switch ($getLang) {
            case ($getLang === 'zh_TW'):
                $this->setCookie('zh_TW', $getSecurity);
                break;
            case ($getLang === 'en_US'):
                $this->setCookie('en_US', $getSecurity);
                break;
            case ($getLang === 'ja_JP'):
                $this->setCookie('ja_JP', $getSecurity);
                break;
            case ($getLang === 'th_TH'):
                $this->setCookie('th_TH', $getSecurity);
                break;
            default:
                $this->setCookie('en_US', $getSecurity);
                break;
        }
    }

    public function getWebLanguage($connectdb)
    {
        $lang['query'] = 'SELECT web_language FROM global_config WHERE id = ?';
        $lang['stmt'] = $connectdb->stmt_init();
        if ($lang['stmt']->prepare($lang['query'])) {
            $lang['stmt']->bind_param('i', self::$config_id);
            $lang['stmt']->execute();
            $lang['stmt']->bind_result($web_language);
            $lang['result'] = $lang['stmt']->get_result();
            $lang['row'] = $lang['result']->fetch_assoc();
            if ($lang['row']['web_language'] != '') {
                return $lang['row']['web_language'];
            } else {
                return 'en_US';
            }
        }
    }
}
