<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Duration extends Pepit_Form_Element_Text
{

    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "filters" => array('HtmlEntities','StripTags','StringTrim',),
        "validators" => array('Float',),
        "multioptions" => array()
        ))->setLabel('item_duration');
        parent::init();
    }


}

