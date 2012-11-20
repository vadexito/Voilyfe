<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Name extends Pepit_Form_Element_Text
{

    protected $_id = null;

    public function init()
    {
        $this->_id = 2;
        $this->setOptions(array(
        "required" => true,
        "filters" => array('HtmlEntities','StringTrim',),
        "validators" => array(),
        "multioptions" => array(),
        ))->setLabel('item_name');
        
        parent::init();
    }


}

