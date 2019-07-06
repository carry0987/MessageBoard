<?php
class Template
{
    private $replacecode = array('search' => array(), 'replace' => array());
    private $blocks = array();
    const DIR_SEP = DIRECTORY_SEPARATOR;
    private static $instance;
    private $options = array();

    //Get Instance
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //Construct options
    private function __construct()
    {
        $this->options = array(
            'template_dir' => 'templates'.self::DIR_SEP,
            'css_dir' => 'css'.self::DIR_SEP,
            'js_dir' => 'js'.self::DIR_SEP,
            'cache_dir' => 'templates'.self::DIR_SEP.'cache'.self::DIR_SEP,
            'auto_update' => false,
            'cache_lifetime' => 0,
            'cache_db' => false
        );
    }

    //Set template parameter array
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setTemplate($name, $value);
        }
    }

    //Set template parameter
    private function setTemplate($name, $value)
    {
        switch ($name) {
            case 'template_dir':
                $value = $this->trimPath($value);
                if (!file_exists($value)) {
                    $this->throwError('Couldn\'t found the specified template folder', $value);
                }
                $this->options['template_dir'] = $value;
                break;
            case 'css_dir':
                $value = $this->trimPath($value);
                if (!file_exists($value)) {
                    $this->throwError('Couldn\'t found the specified css folder', $value);
                }
                $this->options['css_dir'] = $value;
                break;
            case 'js_dir':
                $value = $this->trimPath($value);
                if (!file_exists($value)) {
                    $this->throwError('Couldn\'t found the specified js folder', $value);
                }
                $this->options['js_dir'] = $value;
                break;
            case 'cache_dir':
                $value = $this->trimPath($value);
                if (!file_exists($value)) {
                    $makepath = $this->makePath($value);
                    if ($makepath !== true) {
                        $this->throwError('Can\'t build template folder', $makepath);
                    }
                }
                $this->options['cache_dir'] = $value;
                break;
            case 'auto_update':
                $this->options['auto_update'] = (boolean) $value;
                break;
            case 'cache_lifetime':
                $this->options['cache_lifetime'] = (float) $value;
                break;
            case 'cache_db':
                if ($value === false) {
                    $this->options['cache_db'] = false;
                } else {
                    $this->options['cache_db'] = $value;
                }
                break;
            default:
                $this->throwError('Unknow template setting options', $name);
                break;
        }
    }

    public function __set($name, $value)
    {
        $this->setTemplate($name, $value);
    }

    private function generateRandom($length, $numeric = 0)
    {
        $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash = $hash.$seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /* Static file cache */
    //Get CSS file path
    private function trimCSSName($file)
    {
        return str_replace('.css', '', $file);
    }

    private function getCSSFile($file)
    {
        return $this->trimPath($this->options['css_dir'].self::DIR_SEP.$file);
    }

    //Get CSS version file path
    private function getCSSVersionFile($file)
    {
        $file = preg_replace('/\.[a-z0-9\-_]+$/i', '.cssversion.txt', $file);
        return $this->trimPath($this->options['cache_dir'].self::DIR_SEP.$file);
    }

    //Store CSS version value
    private function cssSaveVersion($file)
    {
        //Get CSS file
        $css_file = $this->getCSSFile($file);
        //Check file if readable
        if (!is_readable($css_file)) {
            $this->throwError('CSS file not found or couldn\'t be opened', $css_file);
        }
        //Add md5 check
        $md5data = md5_file($css_file);
        //Random length random()
        $verhash = $this->generateRandom(7);
        //Insert md5 & verhash
        if ($this->options['cache_db'] !== false) {
            if ($this->getVersion($this->dashPath($this->options['css_dir']), $this->trimCSSName($file), 'css') !== false) {
                $this->updateVersion($this->dashPath($this->options['css_dir']), $this->trimCSSName($file), 'css', $md5data, '0', $verhash);
            } else {
                $this->createVersion($this->dashPath($this->options['css_dir']), $this->trimCSSName($file), 'css', $md5data, '0', $verhash);
            }
        } else {
            $versionContent = $md5data."\r\n".$verhash;
            //Write version file
            $versionfile = $this->getCSSVersionFile($file);
            $makepath = $this->makePath($versionfile);
            if ($makepath !== true) {
                $this->throwError('Couldn\'t build CSS version folder', $makepath);
            }
            file_put_contents($versionfile, $versionContent);
        }
        return $verhash;
    }

    //Check CSS file's change
    private function cssVersionCheck($file)
    {
        if ($this->options['cache_db'] !== false) {
            $css_file = $this->trimCSSName($file);
            $static_data = $this->getVersion($this->dashPath($this->options['css_dir']), $css_file, 'css');
            $md5data = $static_data['tpl_md5'];
            $verhash = $static_data['tpl_verhash'];
            if (md5_file($this->getCSSFile($file)) !== $md5data) {
                $verhash = $this->cssSaveVersion($file);
            }
        } else {
            $versionfile = $this->getCSSVersionFile($file);
            //Get file contents
            $versionContent = file($versionfile, FILE_IGNORE_NEW_LINES);
            $md5data = $versionContent[0];
            $verhash = $versionContent[1];
            if (md5_file($this->getCSSFile($file)) !== $md5data) {
                $verhash = $this->cssSaveVersion($file);
            }
        }
        return $verhash;
    }

    //Load CSS files
    public function loadCSSFile($file)
    {
        if ($this->options['cache_db'] !== false) {
            $css_file = $this->trimCSSName($file);
            $css_version = $this->getVersion($this->dashPath($this->options['css_dir']), $css_file, 'css');
            if ($css_version === false) {
                $this->cssSaveVersion($file);
            }
        } else {
            $versionfile = $this->getCSSVersionFile($file);
            if (!file_exists($versionfile)) {
                $this->cssSaveVersion($file);
            }
        }
        $verhash = $this->cssVersionCheck($file);
        $file = $this->getCSSFile($file);
        return $file.'?v='.$verhash;
    }

    //Get JS file path
    private function trimJSName($file)
    {
        return str_replace('.js', '', $file);
    }

    private function getJSFile($file)
    {
        return $this->trimPath($this->options['js_dir'].self::DIR_SEP.$file);
    }

    //Get JS version file path
    private function getJSVersionFile($file)
    {
        $file = preg_replace('/\.[a-z0-9\-_]+$/i', '.jsversion.txt', $file);
        return $this->trimPath($this->options['cache_dir'].self::DIR_SEP.$file);
    }

    //Store JS version value
    private function jsSaveVersion($file)
    {
        //Get JS file
        $js_file = $this->getJSFile($file);
        //Check file if readable
        if (!is_readable($js_file)) {
            $this->throwError('JS file not found or couldn\'t be opened', $js_file);
        }
        //Add md5 check
        $md5data = md5_file($js_file);
        //Random length random()
        $verhash = $this->generateRandom(7);
        //Insert md5 & verhash
        if ($this->options['cache_db'] !== false) {
            if ($this->getVersion($this->dashPath($this->options['js_dir']), $this->trimJSName($file), 'js') !== false) {
                $this->updateVersion($this->dashPath($this->options['js_dir']), $this->trimJSName($file), 'js', $md5data, '0', $verhash);
            } else {
                $this->createVersion($this->dashPath($this->options['js_dir']), $this->trimJSName($file), 'js', $md5data, '0', $verhash);
            }
        } else {
            $versionContent = $md5data."\r\n".$verhash;
            //Write version file
            $versionfile = $this->getJSVersionFile($file);
            $makepath = $this->makePath($versionfile);
            if ($makepath !== true) {
                $this->throwError('Couldn\'t build JS version folder', $makepath);
            }
            file_put_contents($versionfile, $versionContent);
        }
        return $verhash;
    }

    //Check JS file's change
    private function jsVersionCheck($file)
    {
        if ($this->options['cache_db'] !== false) {
            $js_file = $this->trimJSName($file);
            $static_data = $this->getVersion($this->dashPath($this->options['js_dir']), $js_file, 'js');
            $md5data = $static_data['tpl_md5'];
            $verhash = $static_data['tpl_verhash'];
            if (md5_file($this->getJSFile($file)) !== $md5data) {
                $verhash = $this->jsSaveVersion($file);
            }
        } else {
            $versionfile = $this->getJSVersionFile($file);
            //Get file contents
            $versionContent = file($versionfile, FILE_IGNORE_NEW_LINES);
            $md5data = $versionContent[0];
            $verhash = $versionContent[1];
            if (md5_file($this->getJSFile($file)) !== $md5data) {
                $verhash = $this->jsSaveVersion($file);
            }
        }
        return $verhash;
    }

    //Load JS files
    public function loadJSFile($file)
    {
        if ($this->options['cache_db'] !== false) {
            $js_file = $this->trimJSName($file);
            $js_version = $this->getVersion($this->dashPath($this->options['js_dir']), $js_file, 'js');
            if ($js_version === false) {
                $this->jsSaveVersion($file);
            }
            $verhash = $this->jsVersionCheck($file);
        } else {
            $versionfile = $this->getJSVersionFile($file);
            if (!file_exists($versionfile)) {
                $this->jsSaveVersion($file);
            }
            $verhash = $this->jsVersionCheck($file);
        }
        $file = $this->getJSFile($file);
        return $file.'?v='.$verhash;
    }

    /* Template file cache */
    public function loadTemplate($file)
    {
        if ($this->options['cache_db'] !== false) {
            $versionContent = $this->getVersion($this->dashPath($this->options['template_dir']), $file, 'html');
            if ($versionContent === false) {
                $this->parseTemplate($file);
            }
            $this->checkTemplate($file);
            $cachefile = $this->getTplCache($file);
            if (!file_exists($cachefile)) {
                $this->parseTemplate($file);
            }
        } else {
            $versionfile = $this->getTplVersionFile($file);
            if (!file_exists($versionfile)) {
                $this->parseTemplate($file);
            }
            $this->checkTemplate($file);
            $cachefile = $this->getTplCache($file);
            if (!file_exists($cachefile)) {
                $this->parseTemplate($file);
            }
        }
        return $cachefile;
    }

    private function checkTemplate($file)
    {
        if ($this->options['cache_db'] !== false) {
            $versionContent = $this->getVersion($this->dashPath($this->options['template_dir']), $file, 'html');
            if ($versionContent !== false) {
                $md5data = $versionContent['tpl_md5'];
                $expireTime = $versionContent['tpl_expire_time'];
                if ($this->options['auto_update'] === true && md5_file($this->getTplFile($file)) != $md5data) {
                    $this->parseTemplate($file);
                }
                if ($this->options['cache_lifetime'] != 0 && (time() - $expireTime >= $this->options['cache_lifetime'] * 60)) {
                    $this->parseTemplate($file);
                }
            } else {
                $this->parseTemplate($file);
            }
        } else {
            $versionfile = $this->getTplVersionFile($file);
            $versionContent = file($versionfile, FILE_IGNORE_NEW_LINES);
            $md5data = $versionContent[0];
            $expireTime = $versionContent[1];
            if ($this->options['auto_update'] === true && md5_file($this->getTplFile($file)) != $md5data) {
                $this->parseTemplate($file);
            }
            if ($this->options['cache_lifetime'] != 0 && (time() - $expireTime >= $this->options['cache_lifetime'] * 60)) {
                $this->parseTemplate($file);
            }
        }
    }

    //Parse template file
    private function parseTemplate($file)
    {
        $tplfile = $this->getTplFile($file);
        if (!is_readable($tplfile)) {
            $this->throwError('Template file can\'t be found or opened', $tplfile);
        }

        //Get template contents
        $template = file_get_contents($tplfile);
        $var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
        $const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";
        $template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);

        //Language
        $template = preg_replace_callback("/\{lang\s+(\S+)\s+(\S+)\}/is", array($this, 'parse_language_var_1'), $template);

        //Filter <!--{}-->
        $template = preg_replace("/\h*\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);

        //Replace eval function
        $template = preg_replace_callback("/\{eval\}\s*(\<\!\-\-)*(.+?)(\-\-\>)*\s*\{\/eval\}/is", array($this, 'parse_evaltags_2'), $template);
        $template = preg_replace_callback("/\{eval\s+(.+?)\s*\}/is", array($this, 'parse_evaltags_1'), $template);

        //Replace direct variable output
        $template = preg_replace("/\{\h*(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\h*\}/s", "<?=\\1?>", $template);
        $template = preg_replace_callback("/\<\?\=\<\?\=$var_regexp\?\>\?\>/s", array($this, 'parse_addquote_1'), $template);

        //Replace $var
        //$template = preg_replace_callback("/$var_regexp/s", array($this, 'parse_addquote_1'), $template);

        //Replace template loading function
        $template = preg_replace_callback("/\{template\s+([a-z0-9_:\/]+)\}/is", array($this, 'parse_stripvtags_template1'), $template);
        $template = preg_replace_callback("/\{template\s+(.+?)\}/is", array($this, 'parse_stripvtags_template1'), $template);

        //Replace echo function
        $template = preg_replace_callback("/\{echo\s+(.+?)\}/is", array($this, 'parse_stripvtags_echo1'), $template);

        //Replace cssloader
        $template = preg_replace_callback("/\{loadcss\s+(.+?)\}/is", array($this, 'parse_stripvtags_css1'), $template);

        //Replace jsloader
        $template = preg_replace_callback("/\{loadjs\s+(.+?)\}/is", array($this, 'parse_stripvtags_js1'), $template);

        //Replace if/else script
        $template = preg_replace_callback("/\{if\s+(.+?)\}/is", array($this, 'parse_stripvtags_if1'), $template);
        $template = preg_replace_callback("/\{elseif\s+(.+?)\}/is", array($this, 'parse_stripvtags_elseif1'), $template);
        $template = preg_replace("/\{else\}/i", "<?php } else { ?>", $template);
        $template = preg_replace("/\{\/if\}/i", "<?php } ?>", $template);

        //Replace loop script
        $template = preg_replace_callback("/\{loop\s+(\S+)\s+(\S+)\}/is", array($this, 'parse_stripvtags_loop12'), $template);
        $template = preg_replace_callback("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/is", array($this, 'parse_stripvtags_loop123'), $template);
        $template = preg_replace("/\{\/loop\}/i", "<?php } ?>", $template);

        //Replace constant
        $template = preg_replace("/\{\h*$const_regexp\h*\}/s", "<?=\\1?>", $template);
        if (!empty($this->replacecode)) {
            $template = str_replace($this->replacecode['search'], $this->replacecode['replace'], $template);
        }

        //Remove php extra space and newline
        $template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

        //Other replace
        $template = preg_replace_callback("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/", array($this, 'parse_transamp_0'), $template);
        $template = preg_replace_callback("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/is", array($this, 'parse_stripscriptamp_12'), $template);
        $template = preg_replace_callback("/\{block\s+(.+?)\}(.+?)\{\/block\}/is", array($this, 'parse_stripblock_12'), $template);
        $template = preg_replace("/\<\?(\s{1})/is", "<?php\\1", $template);
        $template = preg_replace("/\<\?\=(.+?)\?\>/is", "<?=\\1;?>", $template);

        //Protect cache file
        $template = '<?php if (!class_exists(\'Template\')) die(\'Access Denied\');?>'."\r\n".$template;

        //Write into cache file
        $cachefile = $this->getTplCache($file);
        $makepath = $this->makePath($cachefile);
        if ($makepath !== true) {
            $this->throwError('Can\'t build template folder', $makepath);
        } else {
            file_put_contents($cachefile, $template."\n");
        }

        if ($this->options['cache_db'] !== false) {
            //Insert md5 and expiretime into cache database
            $md5data = md5_file($tplfile);
            $expireTime = time();
            $versionContent['tpl_md5'] = $md5data;
            $versionContent['tpl_expire_time'] = $expireTime;
            if ($this->getVersion($this->dashPath($this->options['template_dir']), $file, 'html') !== false) {
                $this->updateVersion($this->dashPath($this->options['template_dir']), $file, 'html', $versionContent['tpl_md5'], $versionContent['tpl_expire_time'], '0');
            } else {
                $this->createVersion($this->dashPath($this->options['template_dir']), $file, 'html', $versionContent['tpl_md5'], $versionContent['tpl_expire_time'], '0');
            }
        } else {
            //Add md5 and expiretime check
            $md5data = md5_file($tplfile);
            $expireTime = time();
            $versionContent = "$md5data\r\n$expireTime";
            $versionfile = $this->getTplVersionFile($file);
            file_put_contents($versionfile, $versionContent);
        }
    }

    private function dashPath($path)
    {
        $path = ltrim($path, '/\\');
        $path = rtrim($path, '/\\');
        return str_replace(array('/', '\\', '//', '\\\\'), '-', $path);
    }

    private function trimTplName($file)
    {
        return str_replace('.html', '', $file);
    }

    private function trimPath($path)
    {
        return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
    }

    private function getTplFile($file)
    {
        return $this->trimPath($this->options['template_dir'].self::DIR_SEP.$file);
    }

    private function getTplCache($file)
    {
        $file = preg_replace('/\.[a-z0-9\-_]+$/i', '.cache.php', $file);
        return $this->trimPath($this->options['cache_dir'].self::DIR_SEP.$file);
    }

    private function getTplVersionFile($file)
    {
        $file = preg_replace('/\.[a-z0-9\-_]+$/i', '.htmlversion.txt', $file);
        return $this->trimPath($this->options['cache_dir'].self::DIR_SEP.$file);
    }

    private function getVersion($get_tpl_path, $get_tpl_name, $get_tpl_type)
    {
        $get_tpl_name = $this->trimTplName($get_tpl_name);
        $tpl_query = 'SELECT tpl_md5, tpl_expire_time, tpl_verhash FROM template WHERE tpl_path = ? AND tpl_name = ? AND tpl_type = ?';
        $tpl_stmt = $this->options['cache_db']->stmt_init();
        try {
            $tpl_stmt->prepare($tpl_query);
            $tpl_stmt->bind_param('sss', $get_tpl_path, $get_tpl_name, $get_tpl_type);
            $tpl_stmt->execute();
            $tpl_stmt->bind_result($tpl_md5, $tpl_expire_time, $tpl_verhash);
            $tpl_result = $tpl_stmt->get_result();
            if ($tpl_result->num_rows != 0) {
                $tpl_row = $tpl_result->fetch_assoc();
                return $tpl_row;
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

    private function createVersion($tpl_path, $tpl_name, $tpl_type, $tpl_md5, $tpl_expire_time, $tpl_verhash)
    {
        $tpl_name = $this->trimTplName($tpl_name);
        $tpl_query = 'INSERT INTO template (tpl_path, tpl_name, tpl_type, tpl_md5, tpl_expire_time, tpl_verhash) VALUES (?,?,?,?,?,?)';
        $tpl_stmt = $this->options['cache_db']->stmt_init();
        try {
            $tpl_stmt->prepare($tpl_query);
            $tpl_stmt->bind_param('ssssis', 
                $tpl_path, 
                $tpl_name, 
                $tpl_type, 
                $tpl_md5, 
                $tpl_expire_time, 
                $tpl_verhash
            );
            $tpl_stmt->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    private function updateVersion($tpl_path, $tpl_name, $tpl_type, $tpl_md5, $tpl_expire_time, $tpl_verhash)
    {
        $tpl_name = $this->trimTplName($tpl_name);
        $tpl_query = 'UPDATE template SET tpl_md5 = ?, tpl_expire_time = ?, tpl_verhash = ? WHERE tpl_path = ? AND tpl_name = ? AND tpl_type = ?';
        $tpl_stmt = $this->options['cache_db']->stmt_init();
        try {
            $tpl_stmt->prepare($tpl_query);
            $tpl_stmt->bind_param('sissss', 
                $tpl_md5, 
                $tpl_expire_time, 
                $tpl_verhash, 
                $tpl_path, 
                $tpl_name, 
                $tpl_type
            );
            $tpl_stmt->execute();
        } catch (mysqli_sql_exception $e) {
            echo '<h1>Service unavailable</h1>'."\n";
            echo '<h2>Error Info :'.$e->getMessage().'</h2>'."\n";
            echo '<h3>Error Code :'.$e->getCode().'</h3>'."\n";
            exit();
        }
    }

    private function makePath($path)
    {
        $dirs = explode(self::DIR_SEP, dirname($this->trimPath($path)));
        $tmp = '';
        foreach ($dirs as $dir) {
            $tmp = $tmp.$dir.self::DIR_SEP;
            if (!file_exists($tmp) && !mkdir($tmp, 0777)) {
                return $tmp;
            }
        }
        return true;
    }

    private function parse_language_var_1($matches)
    {
        return $this->stripvTags('<? echo Template::langParam('.$matches[1].', '.$matches[2].');?>');
    }

    private function parse_blocktags_1($matches)
    {
        return $this->blockTags($matches[1]);
    }

    private function parse_evaltags_1($matches)
    {
        return $this->evalTags($matches[1]);
    }

    private function parse_evaltags_2($matches)
    {
        return $this->evalTags($matches[2]);
    }

    private function parse_addquote_1($matches)
    {
        return $this->addQuote('<?='.$matches[1].'?>');
    }

    private function parse_stripvtags_template1($matches)
    {
        return $this->stripvTags('<? include(Template::getInstance()->loadTemplate(\''.$matches[1].'.html\'));?>');
    }

    private function parse_stripvtags_css1($matches)
    {
        return $this->stripvTags('<? echo Template::getInstance()->loadCSSFile(\''.$matches[1].'\');?>');
    }

    private function parse_stripvtags_js1($matches)
    {
        return $this->stripvTags('<? echo Template::getInstance()->loadJSFile(\''.$matches[1].'\');?>');
    }

    private function parse_stripvtags_echo1($matches)
    {
        return $this->stripvTags('<? echo '.$matches[1].';?>');
    }

    private function parse_stripvtags_if1($matches)
    {
        return $this->stripvTags('<? if ('.$matches[1].') { ?>');
    }

    private function parse_stripvtags_elseif1($matches)
    {
        return $this->stripvTags('<? } elseif ('.$matches[1].') { ?>');
    }

    private function parse_stripvtags_loop12($matches)
    {
        return $this->stripvTags('<? if (is_array('.$matches[1].')) foreach ('.$matches[1].' as '.$matches[2].') { ?>');
    }

    private function parse_stripvtags_loop123($matches)
    {
        return $this->stripvTags('<? if (is_array('.$matches[1].')) foreach ('.$matches[1].' as '.$matches[2].' => '.$matches[3].') { ?>');
    }

    private function parse_transamp_0($matches)
    {
        return $this->transAmp($matches[0]);
    }

    private function parse_stripscriptamp_12($matches)
    {
        return $this->stripScriptAmp($matches[1], $matches[2]);
    }

    private function parse_stripblock_12($matches)
    {
        return $this->stripBlock($matches[1], $matches[2]);
    }

    public static function langParam($value, $param)
    {
        foreach ($param as $index => $p) {
            $value = str_replace('{'.$index.'}', $p, $value);
        }
        return $value;
    }

    private function stripBlock($var, $s)
    {
        $s = preg_replace("/<\?=\\\$(.+?)\?>/", "{\$\\1}", $s);
        preg_match_all("/<\?=(.+?)\?>/", $s, $constary);
        $constadd = '';
        $constary[1] = array_unique($constary[1]);
        foreach($constary[1] as $const) {
            $constadd .= '$__'.$const.' = '.$const.';';
        }
        $s = preg_replace("/<\?=(.+?)\?>/", "{\$__\\1}", $s);
        $s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
        $s = str_replace('<?', "\nEOF;\n", $s);
        $s = str_replace("\nphp ", "\n", $s);
        return "<?\n$constadd\$$var = <<<EOF".$s."EOF;\n?>";
    }

    private function evalTags($php)
    {
        $php = str_replace('\"', '"', $php);
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = '<!--EVAL_TAG_'.$i.'-->';
        $this->replacecode['replace'][$i] = '<? '."\n".$php."\n".'?>';
        return $search;
    }

    private function stripPHPCode($type, $code)
    {
        $this->phpcode[$type][] = $code;
        return '{phpcode:'.$type.'/'.(count($this->phpcode[$type]) - 1).'}';
    }

    private function getPHPTemplate($content)
    {
        $pos = strpos($content, "\n");
        return $pos !== false ? substr($content, $pos + 1) : $content;
    }

    private function transAmp($str)
    {
        $str = str_replace('&', '&amp;', $str);
        $str = str_replace('&amp;amp;', '&amp;', $str);
        return $str;
    }

    private function addQuote($var)
    {
        return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
    }

    private function stripvTags($expr, $statement = '')
    {
        $expr = str_replace('\\\"', '\"', preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
        $statement = str_replace('\\\"', '\"', $statement);
        return $expr.$statement;
    }

    private function stripScriptAmp($s, $extra)
    {
        $s = str_replace('&amp;', '&', $s);
        return "<script src=\"$s\"$extra></script>";
    }

    //Throw error excetpion
    private function throwError($message, $tplname)
    {
        throw new Exception($tplname.' : '.$message);
        exit();
    }
}
