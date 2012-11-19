<?php

/**
 * @group Controllers
 */

class LocaleControllerTest extends Pepit_Test_ControllerTestCase
{
    /**
     * 
     * @dataProvider providerLocales
     */
    public function testChangeLocale($locale)
    {
        $this->dispatch($this->vr->view->url(
            array(
                'localeName' => $locale
            ),
            'set-locale'
        ));
        
        $session = new Zend_Session_Namespace('Mylife_locale');
        $this->assertEquals($locale,$session->locale); 
        $this->assertRedirect();
    }
    
    public function providerLocales()
    {
        return array(
          array('fr_FR'), 
          array('en_GB'),  
          array('de_DE'),  
          array('pl_PL')
        );
    }
    
    /**
     * 
     * @expectedException Pepit_Controller_Exception 
     */
    public function estChangeLocaleThrowException()
    {//@ML-TODO tets to do (does not work)
        $locale = 'falseLocale';
        $this->dispatch($this->vr->view->url(
            array(
                'localeName' => $locale
            ),
            'set-locale'
        ));
    }
}
