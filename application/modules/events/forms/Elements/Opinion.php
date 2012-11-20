<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Opinion extends Pepit_Form_Element_Textarea
{

    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "filters" => array('HtmlEntities','StripTags','StringTrim',),
        "validators" => array(),
        "multioptions" => array(),
        ));
        parent::init();
    }


}

