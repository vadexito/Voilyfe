<?php

class Members_Form_Elements_Country extends Pepit_Form_Element_Select
{
    public function init()
    {
        $this      ->setRequired('true')
                   ->setAttrib('data-property-name','country')
                   ->setLabel(ucfirst($this->getTranslator()->translate('item_country')));
        
        if ($this->getEntityManager()->getRepository('ZC\Entity\ItemMulti\Country'))
        {
            $listCountries = Zend_Locale_Data::getList(Zend_Locale::findLocale(),'territory');
            foreach ($listCountries as $key => $country)
            {
                
                if (!is_numeric($key))
                {
                    $this->addMultioption($key,$country);
                }
            }
        }
        parent::init();
    }
    
    
    
}

