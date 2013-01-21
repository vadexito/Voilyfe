<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Price extends Pepit_Form_Element_Range
{

    protected $_id = null;
    
    protected $_min;
    protected $_max;
    protected $_step;

    public function init()
    {
        parent::init();
        
        $this->_id = 5;
        $this->setOptions([
        "required" => false,
        "validators" => ['Float'],
        "value" => 0
        ])->setLabel('item_price');
        
        $this->setAttribs([
            'min' => '0',
            'max' => '200',
            'step' => '2',
        ]);
       
    }
    
    public function dataChart($events)
    {
        $options = [
            'title' => ucfirst($this->getTranslator()->translate('title_amount')),
            'type' => 'sum',
            'propertyForAdding' => 'price',
            'unit' => '€'
        ];
        
        return [
            'type' => 'google_chart',
            'data' => (new Pepit_Widget_Chart($events,$options))->dataForGoogleCharts()
        ];
    }
}

