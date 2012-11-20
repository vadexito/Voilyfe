<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Address extends Pepit_Form_Element_Text
{

    protected $_id = null;

    public function init()
    {
        $this->_id = 3;
        $this->setOptions(array(
        "required" => false,
        "filters" => array('HtmlEntities','StringTrim',),
        "validators" => array(),
        "multioptions" => array(),
        ))->setLabel('item_address');
        
        parent::init();
    }


}

