<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Quantity extends Pepit_Form_Element_Text
{

    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "filters" => array('HtmlEntities','StripTags','StringTrim',),
        "validators" => array('Float',),
        "multioptions" => array(),
        ));
        parent::init();
    }


}

