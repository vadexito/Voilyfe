<?php

class Application_View_Helper_MinifyJs extends Zend_View_Helper_Abstract
{
    protected $_suffixes = ['-min','.min'];
    
    public function minifyJs($file)
    {
        if (APPLICATION_ENV === 'production')
        {
            foreach ($this->_suffixes as $suffix)
            {
                $min = preg_replace('#(.*).js$#', '$1'.$suffix.'.js', $file);
                if (file_exists(APPLICATION_PATH.'/../public'.$min))
                {
                    return $min;
                }
            }
        }
        
        return $file;
    }
}
