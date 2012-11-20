<?php

class Pepit_Array 
{
    /**
     * transform an array (standard) in the same array but with index i+1 instead of i
     * @param array $array 
     */
    
    static public function arrayBeginKeyOne(Array $array)
    {
        // if nothing in the array we return null
        if (empty($array)) return NULL;
        
        $res = array();
        foreach ($array as $key => $value)
        {
            $res[$key+1] = $value;
        }
        return $res;
    }
}