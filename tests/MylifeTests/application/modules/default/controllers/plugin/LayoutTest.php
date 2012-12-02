<?php


/**
 * @group Controllers
 */


class LayoutTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    protected $_plugin;
    
    public function setUp()
    {
        parent::setUp();
        include_once APPLICATION_PATH.'/modules/default/controllers/plugins/Layout.php';
        $this->_plugin = new Application_Controller_Plugin_Layout();
    }
    
    
    /**
     * 
     * @dataProvider providerUrlMvc
     */
    public function testChooseLayout($urlMvc)
    {
        $this->dispatch('events/event/index');
        $this->_plugin->setRequest($this->getRequest());
        $this->assertEquals($this->_plugin->getLayout(),'jk');
        
    }
    
    public function providerUrlMvc()
    {
        return [
          ['urlMvc' => ['module' => 'hkjh', 'controller' => 'event','action' => 'hkjh'],'layout' => 'jlkj'], 
          
        ];
    }
    
    
}
