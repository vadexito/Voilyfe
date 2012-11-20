<?php

class Application_View_Helper_ScriptFileExists extends Zend_View_Helper_Abstract
{
    public function scriptFileExists($viewName,$controllerName)
    {
        $paths = $this->view->getScriptPaths();
        $viewName = $controllerName.'/'.$viewName;
        foreach ($paths as $path)
        {  
            if (file_exists($path . $viewName . '.phtml'))
            {  
                return true;  
            }  
        }  
        return false;  
    }  
}
