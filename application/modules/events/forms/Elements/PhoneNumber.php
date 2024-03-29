<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_PhoneNumber extends Events_Form_Elements_Abstract_Text
{

    protected $_id = null;

    public function init()
    {
        $this->_id = 1;
        $this->setOptions(array(
        "required" => false,
        "filters" => array('HtmlEntities','StringTrim',),
        "validators" => array(),
        "multioptions" => array(),
        ))->setLabel('item_phoneNumber');;
        
        parent::init();
    }
}

