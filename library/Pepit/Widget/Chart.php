<?php

/*
* @author DM
* 
*/
class Pepit_Widget_Chart
{
    
    protected $_events;
    protected $_titleYAxis = NULL;
    protected $_width = NULL;
    protected $_height = NULL;
    protected $_desc = false;
    protected $_periodNb = 6;
    protected $_type = 'frequency';
    protected $_propertyForAdding = NULL;
    protected $_unit = NULL;
    protected $_legend= 'left';
    protected $_title = NULL;
    protected $_timeUnit = 'month';
    protected $_timeUnitNb = 1;
    protected $_hAxisTitle = 'month';
            
    
    
    public function __construct($events,$options = NULL)
    {
        $this->_events= $events;
        $this->setOptions($options);
    }
    
    public function setOptions($options)
    {
        $properties = ['titleYAxis','width','height','desc','periodNb',
            'type','propertyForAdding','unit','legend','title','timeUnit',
            'timeUnitNb','hAxisTitle'];
        foreach ($properties as $property)
        {
            $protectedProp = '_'.$property;
            if (key_exists($property,$options))
            {
                $this->$protectedProp = $options[$property];
            }    
        }
    }
    
    /**
     * 
     * @return array
     */
    public function dataForGoogleCharts()
    {
        
        $dataChart = $this->addValues();
        
        $dataChartFinal = [['month',$this->_titleYAxis]];
        foreach($dataChart as $namePeriod => $timePeriod)
        {
            $dataChartFinal[] = [$namePeriod,$timePeriod['parameterValue']];
        }
        
        return [
            'values' => $dataChartFinal,
            'options' => [
                'title'         => $this->_title,
                'width'         => $this->_width,
                'height'        => $this->_height,
                'hAxisTitle'    => $this->_hAxisTitle,
                'vAxisTitle'    => $this->_unit,
                'legend'        => $this->_legend
        ]];
    }
    
    protected function _createPeriods()
    {
        $now = new Zend_Date();
        $currentMonth = $now->toString(Zend_Date::MONTH);
        $currentYear = $now->toString(Zend_Date::YEAR);
        
        switch ($this->_timeUnit)
        {
            case 'year' : 
                $yearBegin = $currentYear;
                $monthBegin = 12;
                $dayBegin = cal_days_in_month(
                        CAL_GREGORIAN,
                        $monthBegin,
                        $yearBegin
                );
                $periodNameFormat = ' '.Zend_date::YEAR;
                $periodLength = ['unit' => Zend_Date::MONTH,'nb' =>  12 * $this->_timeUnitNb];
            break;
            
            case 'month':
            default:
                $yearBegin = $currentYear;
                $monthBegin = $currentMonth;
                $dayBegin = cal_days_in_month(
                        CAL_GREGORIAN,
                        $monthBegin,
                        $yearBegin
                );
                $periodNameFormat = Zend_Date::MONTH_NAME_SHORT.' '.Zend_date::YEAR_SHORT;
                $periodLength = ['unit' => Zend_Date::MONTH,'nb' => $this->_timeUnitNb];
        }
        
        $begin = new Zend_Date($yearBegin.'-'.$monthBegin.'-'.$dayBegin);
        
        $date = $begin;
        $dataChart = [];
        
        //create the periods
        for($i=0; $i < $this->_periodNb; $i++)
        {
            $periodName = $date->toString($periodNameFormat);
            $dataChart[$periodName] = [
                'parameterValue' => 0,
                'end'       => $date->toString(Zend_Date::TIMESTAMP),
                'begin'     => $date->sub($periodLength['nb'],$periodLength['unit'])
                                    ->toString(Zend_Date::TIMESTAMP),
            ];
        }
        
        if (!$this->_desc)
        {
            return array_reverse($dataChart);
        }
        
        return $dataChart;
    }
    
    protected function addValues()
    {
        $dataChart = $this->_createPeriods();
        //adding of the number of times in each period
        $incrementValueFunc = $this->getIncrementValueFunc();
        foreach ($this->_events as $event)
        {
            $date = $event->date->getTimeStamp();
            $delta = NULL;
            
            $property = strtolower($event->category->name).'_'.$this->_propertyForAdding;
            if ($this->_propertyForAdding && property_exists($event,$property))
            {
                $delta = $event->$property;
            }
            elseif ($this->_propertyForAdding)
            {
                throw new Pepit_Model_Exception('The event '
                        .get_class($event).' does not have the property :'
                        .$this->_propertyForAdding);
            }
            
            foreach($dataChart as $periodIndex => $period)
            {
                if ($date>=$period['begin'] && $date<=$period['end'])
                {
                    $dataChart[$periodIndex]['parameterValue'] = 
                    call_user_func_array(
                            $incrementValueFunc,
                            [$dataChart[$periodIndex]['parameterValue'],$delta]
                    );
                }
            }
        }
        
        return $dataChart;
    }
    
    protected function getIncrementValueFunc()
    {
        $addUnit = function ($init){
            
            return $init+1;
        };
            
        $sumFunc = function($init,$delta){
            return $init+$delta;
        };
        
        switch($this->_type)
        {
            case 'sum' :
                return $sumFunc;
            case 'frequency':
            default:
                return $addUnit;
        }
    }
}
