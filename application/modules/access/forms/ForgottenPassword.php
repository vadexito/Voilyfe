<?php

class Access_Form_ForgottenPassword extends Pepit_Form
{

    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
    }
}

