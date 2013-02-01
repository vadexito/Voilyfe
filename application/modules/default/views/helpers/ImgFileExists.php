<?php

class Application_View_Helper_ImgFileExists extends Zend_View_Helper_Abstract
{
    public function ImgFileExists($src)
    {
        $path = APPLICATION_PATH.'/../public';
        return file_exists($path . $src);
    }  
}
