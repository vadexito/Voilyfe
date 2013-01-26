<?php
/**
 * form element for item
 *
 * @author DM
 */


abstract class Events_Form_Elements_Abstract_Textarea extends Pepit_Form_Element_Textarea
{

    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "filters" => array('StripTags','StringTrim',),
        ));
        
        $this->addClass('event_item');
        parent::init();
    }


}

