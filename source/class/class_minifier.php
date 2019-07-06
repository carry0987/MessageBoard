<?php
class Minifier
{
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
            'css_dir' => 'css'.self::DIR_SEP,
            //Specify your css-files and their order here
            'css_file' => array('style.css'),
            'minified_dir' => 'data'.self::DIR_SEP.'minified'.self::DIR_SEP,
            'minified_name' => 'style.min.css'
        );
    }

    //Set minifier parameter array
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setMinifier($name, $value);
        }
    }

    //Set minifier parameter
    private function setMinifier($name, $value)
    {
        switch ($name) {
            case 'css_dir':
                $value = $this->trimPath($value);
                if (!file_exists($value)) {
                    $this->throwError('Couldn\'t found the specified css folder', $value);
                }
                $this->options['css_dir'] = $value;
                break;
            case 'minified_dir':
                $value = $this->trimPath($value);
                if (!file_exists($value)) {
                    $makepath = $this->makePath($value);
                    if ($makepath !== true) {
                        $this->throwError('Can\'t build minified folder', $makepath);
                    }
                }
                $this->options['minified_dir'] = $value;
                break;
            case 'minified_name':
                $this->options['minified_name'] = $value;
                break;
            default:
                $this->throwError('Unknow minifier setting options', $name);
                break;
        }
    }

    public function __set($name, $value)
    {
        $this->setMinifier($name, $value);
    }

    public function minifyCSS()
    {
        $this->options['css_file'] = func_get_args();
        $file = '';
        foreach($this->options['css_file'] as $cssTemplate) {
            $cssfile = $this->getTplFile($cssTemplate);
            if (!file_exists($cssfile)) {
                $this->throwError('Couldn\'t found css file', $cssfile);
            } else {
                $file .= file_get_contents($cssfile);
            }
        }
        $this->parseCSS($file);
    }

    /**
     * This function takes a css-string and compresses it, removing
     * unneccessary whitespace, colons, removing unneccessary px/em
     * declarations etc.
     *
     * @param string $css
     * @return string compressed css content
     * @author Steffen Becker
     */
    private function parseCSS($file)
    {
        //Remove comments
        $file = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $file);
        //Backup values within single or double quotes
        preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $file, $hit, PREG_PATTERN_ORDER);
        for ($i=0; $i < count($hit[1]); $i++) {
            $file = str_replace($hit[1][$i], '##########'.$i.'##########', $file);
        }
        //Remove traling semicolon of selector's last property
        $file = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $file);
        //Remove any whitespace between semicolon and property-name
        $file = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $file);
        //Remove any whitespace surrounding property-colon
        $file = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $file);
        //Remove any whitespace surrounding selector-comma
        $file = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $file);
        //Remove any whitespace surrounding opening parenthesis
        $file = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $file);
        //Remove any whitespace between numbers and units
        $file = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $file);
        //Shorten zero-values
        $file = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $file);
        //Constrain multiple whitespaces
        $file = preg_replace('/\p{Zs}+/ims', ' ', $file);
        //Remove newlines
        $file = str_replace(array("\r\n", "\r", "\n"), '', $file);
        //Restore backupped values within single or double quotes
        for ($i=0; $i < count($hit[1]); $i++) {
            $file = str_replace('##########'.$i.'##########', $hit[1][$i], $file);
        }
        //Write into css file
        $makepath = $this->makePath($this->options['minified_dir'].$this->options['minified_name']);
        if ($makepath !== true) {
            $this->throwError('Can\'t build minified folder', $makepath);
        } else {
            file_put_contents($this->makeCSSFile(), $file);
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

    private function trimPath($path)
    {
        return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
    }

    private function getTplFile($file)
    {
        return $this->trimPath($this->options['css_dir'].self::DIR_SEP.$file);
    }

    private function makeCSSFile()
    {
        return $this->trimPath($this->options['minified_dir'].self::DIR_SEP.$this->options['minified_name']);
    }

    //Throw error excetpion
    private function throwError($message, $css_tpl_name)
    {
        throw new Exception($css_tpl_name.' : '.$message);
        exit();
    }
}
