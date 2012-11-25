<?php

$a = array('un','hoiuoi','huihuh','poip');
reset($a);

while ($val = current($a))
{
    if ($val === 'poip')
    {
        
        if (!prev($a))
        {
            reset($a);
            $prev = NULL;
        }
        else
        {
            $prev = prev($a);
            next($a);
        }
        $key = key($a);
        $next = next($a);
        
    }
    next($a); 
} 

    
    
    
echo 'previous element : '.$prev.'<br/>';
echo 'researched element : '.$key.'<br/>';
echo 'next element : '.$next.'<br/>';