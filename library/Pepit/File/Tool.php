<?php

class Pepit_File_Tool 
{
    public static function getExtension($name)
    {
        $names= explode(".", $name);
        
        return strtolower($names[count($names)-1]);
    }
    
}
