<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Quantity extends Pepit_Form_Element_RangePlusUnit
{

    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "validators" => ['Float'],
        ));
        parent::init();
    
        $units=[
            Zend_Measure_Weight::GRAM,
            Zend_Measure_Weight::KILOGRAM,
            Zend_Measure_Weight::MILLIGRAM,
            Zend_Measure_Weight::ONCE,
        ];
        foreach ($units as $unit)
        {
            $this->addMultiOption($unit,new Zend_Measure_Weight('0',$unit));
        }
        
        $this->setAttribs([
                'min' => '0',
                'max' => '200',
                'step' => '2',
        ]);
    }
    
    public function dataChart($events)
    {
        $options = [
            'title' => ucfirst($this->getTranslator()->translate('title_total_quantity')),
            'type' => 'sum',
            'propertyForAdding' => $this->getName(),
            'unit' => 'mg'
        ];
        
        return [
            'type' => 'google_chart',
            'data' => (new Pepit_Widget_Chart($events,$options))->dataForGoogleCharts()
        ];
    }


}

