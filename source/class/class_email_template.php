<?php
class EmailTemplate
{
    const DIR_SEP = DIRECTORY_SEPARATOR;
    private static $instance;
    private $option = array();

    //Get Instance
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //Construct option
    private function __construct()
    {
        $this->option = array(
            'template_dir' => 'templates'.self::DIR_SEP
        );
    }

    //Set template parameter array
    public function setOption(array $option)
    {
        foreach ($option as $name => $value) {
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
                $this->option['template_dir'] = $value;
                break;
            default:
                $this->throwError('Unknow template setting option', $name);
                break;
        }
    }

    public function __set($name, $value)
    {
        $this->setTemplate($name, $value);
    }

    //Email Template file
    public function loadEmailTemplate($file, $param)
    {
        $path = $this->trimPath($this->option['template_dir']);
        if (!file_exists($path.$file)) {
            $this->throwError('Could not find the email template file !');
        } else {
            $email = file_get_contents($path.$file);
            $email = $this->replaceParam($email, $param);
        }
        return $email;
    }

    //Replace param
    private function replaceParam($value, $param)
    {
        foreach ($param as $index => $p) {
            $value = str_replace('{'.$index.'}', $p, $value);
        }
        return $value;
    }

    //Trim path
    private function trimPath($path)
    {
        return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
    }

    //Throw error excetpion
    private function throwError($message)
    {
        throw new Exception($message);
        exit();
    }
}
