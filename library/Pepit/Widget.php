<?php

class Pepit_Widget
{
    /**
     * transform an array (standard) in the same array but with index i+1 instead of i
     * @param array $array 
     */
    
    static public function factory($nameWidget,$nameWidgetClass,$options=NULL)
    {
        $class = 'Pepit_Widget_'.ucfirst($nameWidgetClass);
        return new $class($nameWidget,$options);
    }
}