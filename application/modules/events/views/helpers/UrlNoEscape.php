<?php

class Events_View_Helper_UrlNoEscape extends Zend_View_Helper_Abstract
{
    
    public function urlNoEscape(array $arrayProperty,$route = NULL)
    {
        $tempArray = $tempArrayPattern = [];
        $i=0;
        foreach ($arrayProperty as $property => $value)
        {
            $tempArray[$property] = 'replace'.$i;
            $tempArrayPattern[] = '#replace'.$i.'#';
            $i++;  
        }
        
        if ($route)
        {
            return preg_replace(
                $tempArrayPattern,
                array_values($arrayProperty),
                $this->view->url($tempArray,$route)
            );
        }
        else
        {
            return preg_replace(
                $tempArrayPattern,
                array_values($arrayProperty),
                $this->view->url($tempArray)
            );
        }
        
        
    }
}
