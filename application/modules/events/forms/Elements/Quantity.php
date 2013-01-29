<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Quantity extends Pepit_Form_Element_Range
{

    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "validators" => ['Float'],
        ));
        parent::init();
    
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
            'propertyForAdding' => 'quantity',
            'unit' => 'mg'
        ];
        
        return [
            'type' => 'google_chart',
            'data' => (new Pepit_Widget_Chart($events,$options))->dataForGoogleCharts()
        ];
    }


}

