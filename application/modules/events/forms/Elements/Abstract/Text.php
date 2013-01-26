<?php
/**
 * form element for item
 *
 * @author DM
 */


abstract class Events_Form_Elements_Abstract_Text extends Pepit_Form_Element_Text
{
    protected $_id = null;

    public function init()
    {
        $this->setOptions(["filters" => ['StringTrim']]);
        
        $this->addClass('event_item');
        parent::init();
    }


}

