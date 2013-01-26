<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Address extends Events_Form_Elements_Abstract_Text
{

    protected $_id = null;

    public function init()
    {
        $this->_id = 3;
        $this   ->setOptions(array(
                    "required" => false,
                    "filters" => ['StringTrim']
                    ))
                ->setLabel('item_address');
        
        $this->addClass('event_item');
                
        parent::init();
    }


}

