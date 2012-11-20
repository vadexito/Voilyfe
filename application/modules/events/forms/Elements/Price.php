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
        $this->setOptions(array(
        "required" => false,
        "filters" => array(),
        "validators" => array('Float'),
        "value" => 10
        ))->setLabel('item_price');
        
        $this->setAttribs(array(
            'min' => '0',
            'max' => '200',
            'step' => '2',
        ));
    }
}
