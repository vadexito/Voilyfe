<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Meals extends Events_Form_Elements_Abstract_Text
{

    protected $_id = null;

    public function init()
    {
        $this->_id = 4;
        $this->setOptions(array(
        "required" => false,
        "filters" => array('StringTrim',),
        "validators" => array(),
        "multioptions" => array(),
        ))->setLabel('item_meals');;
        
        parent::init();
    }
    
    


}

