<?php
class Load
{
    private $files;
    public function loadClass()
    {
        $this->files = func_get_args();
        foreach ($this->files as $file) {
            require dirname(__FILE__)."/class_$file.php";
        }
    }

    public function loadFunction()
    {
        $this->files = func_get_args();
        foreach ($this->files as $file) {
            require dirname(dirname(__FILE__))."/function/function_$file.php";
        }
    }

    public function loadAjax()
    {
        $this->files = func_get_args();
        foreach ($this->files as $file) {
            require dirname(dirname(__FILE__))."/ajax/ajax_$file.php";
        }
    }

    public function loadSocialClass()
    {
        $this->files = func_get_args();
        foreach ($this->files as $file) {
            require dirname(dirname(__FILE__))."/social/social_$file.php";
        }
    }
}
