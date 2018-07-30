<?php
class Load
{
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

    public function loadInclude()
    {
        $this->files = func_get_args();
        foreach ($this->files as $file) {
            require dirname(dirname(__FILE__))."/include/include_$file.php";
        }
    }
}
