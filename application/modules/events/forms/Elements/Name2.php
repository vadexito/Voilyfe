<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Name2 extends Events_Form_Elements_Abstract_Text
{

    protected $_id = null;

    public function init()
    {
        $this->_id = 2;
        $this->setOptions(array(
        "required" => false,
        "filters" => array('StringTrim',),
        "validators" => array(),
        "multioptions" => array(),
        ));
        parent::init();
    }


}

