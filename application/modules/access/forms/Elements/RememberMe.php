<?php
/**
 * form element for item
 *
 * @author DM
 */


class Access_Form_Elements_RememberMe extends Pepit_Form_Element_Checkbox
{

    public function init()
    {
        $this->setOptions([
            'name'  => 'rememberMe',
            'value' => 1,
            'decorators'=>['ViewHelper','label']
        ]);
        
        $this   ->setLabel('user_rememberMe')
                ->getDecorator('label')->setOptions([
                    'placement'     =>'implicit_append',
                    'class'         =>'checkbox',
                    'escape'        => false,
                    'optionalSuffix'=>' - <a href="'
                        .$this->getView()->url(['action'=> 'getpassword'],'access')
                        .'" >'
                        .$this->getTranslator()->translate('user_forgotten_password')
                        .'</a>',
            ]);
        
        parent::init();
    }


}

