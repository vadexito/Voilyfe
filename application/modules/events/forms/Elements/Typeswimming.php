<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Typeswimming extends Pepit_Form_Element_Select
{
    
    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "idDB" => 17,
        "filters" => array('HtmlEntities','StripTags','StringTrim',),
        "validators" => array(),
        ))->setLabel('item_typeswimming');
        parent::init();
        
        $this->setMultiOptions(array(
            'crawl' => $this->getTranslator()->translate('item_typeswimming_crawl'),
            'breaststroke' => $this->getTranslator()->translate('item_typeswimming_breaststroke'),
            'back' => $this->getTranslator()->translate('item_typeswimming_back'),
        ));
    }


}

