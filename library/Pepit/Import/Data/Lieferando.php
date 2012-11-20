<?php

/**
 *
 * Lieferando
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_Import_Data_Lieferando
{

    public function isPrice($price)
    {
        //return (preg_match('#^\d+([,.](\d){0,2})? €$#', $price));
        return preg_match('#^\d+([,.](\d){0,2})?#', $price);
    }
    
    public function toPrice($price)
    {
        return (float)preg_replace('#^(\d+)([,.]((\d){0,2})?)(.*)$#','$1.$3' ,$price);
    }
    
    public function toDateTime($date)
    {
        $pattern = '#^(\d{2}).(\d{2}).(\d{4}) (\d{2}):(\d{2})$#';
        $match = preg_match($pattern, $date);
        $day = preg_replace($pattern,'$1' ,$date);
        $month = preg_replace($pattern,'$2' ,$date);
        $year = preg_replace($pattern,'$3' ,$date);
        $hour = preg_replace($pattern,'$4' ,$date);
        $minute = preg_replace($pattern,'$5' ,$date);
        
        if ($match)
        {
            return new DateTime(
                $year.'-'.$month.'-'.$day. ' '.$hour.':'.$minute
            );
        }
    }
    
    public function importHtmlToArray($path)
    {
        $lieferandoFile = new DOMDocument();
        $lieferandoFile->loadHTMLFile($path);
        
        $deliveries = array();
        $i=0;
        foreach($lieferandoFile->getElementsByTagName('tr') as $delivery)
        {   
            $details = array();
            $j = 0;
            foreach ($delivery->childNodes as $detail)
            {
                switch($j)
                {
                    case 2 : 
                        $details['date']  = 
                            $this->toDateTime($detail->nodeValue);
                        break;
                    case 4: 
                        $details['restaurantName']  = $detail->nodeValue;
                        break;
                    case 8 : 
                        $details['amount']  = 
                            $this->toPrice($detail->nodeValue);
                        break;
                    case 10 : 
                        $details['meals']  = $detail->nodeValue;
                        break;
                }
                $j++;
            }
            if ($i>1)
            {
                $deliveries[] = $details;
            }
            $i++;
        }
        
        return $deliveries;
    }
    
    public function showStat($path)
    {
        $deliveries = $this->importHtmlToArray($path);
        
        $total = 0;
        $nb = 0;
        $names = array();
        $months = array();
        $stat = array();
        $id = 0;
         
        foreach ($deliveries as $delivery)
        {
            $total+=$delivery['amount'];
            $nb++;
            $names[] = $delivery['restaurantName'];
            
        }
        $names = array_unique($names);
        
        foreach ($deliveries as $delivery)
        {
            if (!isset($stat[$delivery['restaurantName']]))
            {
                $stat[$delivery['restaurantName']] = 0;
                $id++;
            }
            if (!isset($months[$delivery['date']->format('M')]))
            {
                $months[$delivery['date']->format('M')] = 0;
                $id++;
            }
            $months[$delivery['date']->format('M')]++;
            $stat[$delivery['restaurantName']]++; 
        }
        
        arsort($stat);
        
        echo 'Total de '.$nb. ' fois et ' 
                .$total .' € soit en moyenne ' 
                . $total/$nb . ' € pour '.count($names)
                . ' livreurs différents.<br/><br/>';
        
        foreach ($stat as $deliv => $value)
        {
            echo $value. ' raz w ' . $deliv . '<br/>';
        }
        $months = array_reverse($months);
        foreach ($months as $month => $value)
        {
            echo 'En '.$month.' '.$value.' fois.<br/>';
        }
    }
}

