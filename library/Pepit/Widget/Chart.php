<?php

/*
* @author DM
* 
*/
class Pepit_Widget_Chart
{
    
    protected $_events;
    
    protected $_categoryName;
    
    
    public function __construct($events,$categoryName)
    {
        $this->_events= $events;
        $this->_categoryName= $categoryName;
        
    }
    
    public function dataForGoogleCharts($parameter='frequency',
            $periodLength=6,$width=null,$height=null,$desc = false)
    {
        $categoryName = $this->_categoryName;
        
        $dataChart = $this->createPeriod($periodLength,$desc);
        $dataChart = $this->addValues($parameter,$dataChart);
        
        $dataChartFinal = array(array('month',$categoryName));
        foreach($dataChart as $namePeriod => $timePeriod)
        {
            $dataChartFinal[] = array($namePeriod,$timePeriod['parameterValue']);
        }
        
        $unit = '';
        
        return array(
            'values' => $dataChartFinal,
            'options' => array(
                'title'         => null,
                'width'         => $width,
                'height'        => $height,
                'hAxisTitle'    => 'month',
                'vAxisTitle'    => $unit,
                'legend'        => 'left'
        ));
    }
    
    protected function createPeriod($periodLength,$desc=false)
    {
        $now = new Zend_Date();
        $date = $now;
        $dataChart = array();
        
        //create the periods
        for($i=0;$i<$periodLength;$i++)
        {
            $periodName = $date->toString(Zend_Date::MONTH_NAME_SHORT.' '.Zend_date::YEAR_SHORT);
            $dataChart[$periodName] = array(
                'parameterValue' => 0,
                'end'     => $date->toString(Zend_Date::TIMESTAMP),
                'begin'     => $date->sub(1,Zend_Date::MONTH)->toString(Zend_Date::TIMESTAMP),
            );
        }
        
        if (!$desc)
        {
            return array_reverse($dataChart);
        }
        return $dataChart;
    }
    
    protected function addValues($parameter,$dataChart)
    {
        //adding of the number of times in each period
        foreach ($this->_events as $event)
        {
            $date = $event->date->getTimeStamp();
            foreach($dataChart as $key => $period)
            {
                if ($date>=$period['begin'] && $date<=$period['end'])
                {
                    $dataChart[$key]['parameterValue'] = 
                        $this->incrementValueforChart(
                                $dataChart[$key]['parameterValue'],
                                $event,
                                $parameter
                    );
                }
            }
        }
        
        return $dataChart;
    }
    
    protected function incrementValueforChart($initValue,$event,$parameter)
    {
        switch($parameter)
        {
            case 'frequency':
                return $initValue+1;
            default:
                return $initValue + $event->$parameter;
        }
    }

}
