<?php
/**
 * form element for item
 *
 * @author DM
 */


abstract class Events_Form_Elements_Abstract_Tags extends Pepit_Form_Element_Tags
{
    public function init()
    {
        $this->addClass('event_item');
        parent::init();
    }


}

