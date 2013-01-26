<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Name extends Events_Form_Elements_Abstract_Text
{

    public function init()
    {
        $this->_id = 2;
        $this   ->setOptions([
                    "required" => true,
                ])
                ->setLabel('item_name');
        
        parent::init();
    }


}

