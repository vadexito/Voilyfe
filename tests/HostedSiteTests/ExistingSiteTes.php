<?php

class WebTest extends PHPUnit_Extensions_SeleniumTestCase
{

    public function setUp()
    {
        parent::setUp();
        
        $this->setBrowser('*googlechrome C:\Program Files (x86)\Google\Chrome\Application\chrome.exe');
        $this->setBrowserUrl('http://www.voilamylife.com');
        
        
    }
    public function testTitle()
    {
        $this->open('http://www.voilamylife.com');
        $this->assertTitle('Mylife');
        
    }
    
    public function providerBrowsers()
    {
        return array(
            array('*safari C:\Program Files (x86)\Safari\safari.exe'),
            array('*ie7explorer C:\Program Files (x86)\Internet Explorer\iexplore.exe'),
        );
    }

}

