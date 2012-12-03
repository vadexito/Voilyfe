<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Medicine extends Pepit_Form_Element_Select
{

    public function init()
    {
        $this->setOptions(array(
        "required" => true,
        "idDB" => 11,
        "multioptionTarget" => 'ZC\Entity\ItemRow',
        "filters" => array('StringTrim',),
        "validators" => array(),
        ));
        parent::init();
    }


}

