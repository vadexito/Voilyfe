<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initAutoloader()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'  => APPLICATION_PATH.'/modules/default',
            'namespace' => 'Application',
        ));
        $loader->addResourceType('acl', 'acls', 'Acl');
        
        $loaderEvents = new Zend_Loader_Autoloader_Resource(array(
            'basePath'  => APPLICATION_PATH.'/modules/events/controllers',
            'namespace' => 'Events_Controller',
        ));
        $loaderEvents->addResourceType('abstract', 'abstract', 'Abstract');
    }
    
    protected function _initLoadTraitsAndInterfaces()
    {
        include_once 'Pepit/Form/Element/Trait/Trait.php';
        include_once 'Pepit/Form/Element/Interface/Interface.php';
        include_once 'Pepit/Model/Traits/BindForm.php';
        include_once 'Pepit/Model/Traits/Doctrine2.php';
        include_once 'Pepit/Doctrine/Trait.php';
        include_once 'Pepit/Locale/Trait.php';
        include_once APPLICATION_PATH.'/modules/events/controllers/trait/Trait.php';
    }

    protected function _initView()
    {
        // get view
        $view = $this->getPluginResource('view')->getView();
        
        // add title;
        $view->headTitle('Mylife');
 
        //add short cut icon
        $view->headLink([
            'rel' => 'icon',
            'type' => 'image/jpeg',
            'href' => '/images/shortcut_icon.jpg'
        ],'APPEND');
        
        // Add to ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        
        $viewRenderer->setView($view); 
        
        // Return view to be kept by bootstrap
        return $view;
    }
    
    
    protected function _initRegistry()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/config.ini',
                                APPLICATION_ENV);
        
        Zend_Registry::set('config',$config);
        
        Zend_Registry::set(
            'session_history_events',
            new Zend_Session_Namespace('history_events')
        );
        
        
    }
    
    public function _initAcl()
    {
        $acl = new Application_Acl_Acl();
        Zend_Registry::set('acl',$acl);
        $this->getResource('view')->navigation()->setDefaultAcl($acl);         
        
        //return acl for storing in boostrap
        return $acl;
    }
      
    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Application_Controller_Access_Plugin', '../application/modules/access/controllers/plugins/');
        $loader->addPrefixPath('Application_Controller_Plugin', '../application/modules/default/controllers/plugins/');
        
        
        $loader->load('Auth'); 
        $acl = $this->getResource('acl');
        $front->registerPlugin(new Application_Controller_Access_Plugin_Auth($acl));
        
        $loader->load('Navmenu');
        $front->registerPlugin(new Application_Controller_Plugin_Navmenu());
        
        $loader->load('Layout');
        $front->registerPlugin(new Application_Controller_Plugin_Layout());
        
        $loader->load('MobileInit');
        $front->registerPlugin(new Application_Controller_Plugin_MobileInit());
        
    }
    
    
    public function _initLocale()
    {
        $locale = NULL;
        $session = new Zend_Session_Namespace('Mylife_locale');
        
        if ($session->locale)
        {
            $locale = new Zend_Locale($session->locale);
        }
        
        $localeConfig = Zend_Registry::get('config')->get('language',false);
        if ( $localeConfig && Zend_Locale::isLocale($localeConfig))
        {
            $locale = new Zend_Locale($localeConfig);
        }
            
        if ($locale === NULL)
        {
            try
            {
                $locale = new Zend_Locale();
            }
            catch(Zend_Locale_Exception $e)
            {
                $locale = new Zend_Locale('en_GB');
            }
        }
        Zend_Registry::set('Zend_Locale',$locale);
        
        return $locale;
        
    }
    
    public function _initTranslate()
    {
        // load translations and put translate object in the registry
        $translate = new Zend_Translate(array(
            'adapter'       => 'gettext',
            'content'       => APPLICATION_PATH . '/translations/',
            'scan'          => Zend_Translate::LOCALE_FILENAME,
            'disableNotices'=> true
        ));
        
        // load in registry
        Zend_Registry::set('Zend_Translate', $translate);
    }
    
    public function _initTerawurfl()
    {
        require_once('Terawurfl/TeraWurflConfig.php');
        
        $options = $this->getOptions();
        $config = $options['resources']['useragent']['terawurfl']['config'];
        
        TeraWurflConfig::$DB_HOST = $config['db']['host'];
        TeraWurflConfig::$DB_PASS = $config['db']['pass'];
        TeraWurflConfig::$DB_USER = $config['db']['user'];
        TeraWurflConfig::$DB_SCHEMA = $config['db']['name'];
       
    }
    
    public function _initNavigation()
    {
        $container = new Zend_Navigation();
        
        $page1 = Zend_Navigation_Page::factory(array(
            'route' => 'home'
        ));
        $page1->setId('home');
        $page1->setLabel('menu_home');
        $page1->setResource('index');
        $page1->setOrder(1);
        
        $page2 = Zend_Navigation_Page::factory(array(
            'route' => 'event',
            'params' => array('action' => 'index'),
        ));
        $page2->setId('event');
        $page2->setLabel('menu_event');
        $page2->setResource('events:event');
        $page2->setPrivilege('index');
        $page2->setOrder(2);
        
        $page2bis = Zend_Navigation_Page::factory(array(
            'route' => 'event',
            'params' => array('action' => 'index'),
        ));
        $page2bis->setId('eventAdd');
        $page2bis->setLabel('menu_event_add');
        $page2bis->setResource('events:event');
        $page2bis->setPrivilege('create');
        
        $page3 = Zend_Navigation_Page::factory(array(
            'uri' => '#',
        ));
        $page3->setId('metaCategory');
        $page3->setLabel('menu_metaCategory');
        $page3->setResource('events:event');
        $page3->setPrivilege('index');
        $page3->setOrder(3);
        
        $page4 = Zend_Navigation_Page::factory(array(
            'route' => 'default',
            'uri'   => '#'
        ));
        $page4->setId('user');
        $page4->setLabel('menu_user');
        $page4->setResource('members:user');
        $page4->setPrivilege('index');
        $page4->setOrder(4);
        
        $page4_1 = Zend_Navigation_Page::factory(array(
            'route' => 'access',
            'params' => array('action' => 'logout'),
        ));
        $page4_1->setId('logout');
        $page4_1->setLabel('menu_logout');
        $page4_1->setOrder(100);
        
        $page4_2 = Zend_Navigation_Page::factory(array(
            'route' => 'member',
            'params' => array('controller'=> 'settings', 'action' => 'index'),
        ));
        $page4_2->setId('setpreferences');
        $page4_2->setLabel('menu_set_preferences');
        $page4_2->setOrder(1);
        
        
        
        $page5 = Zend_Navigation_Page::factory(array(
            'route' => 'default',
            'uri'   => '#',
            'label' => 'Backend'
        ));
        $page5->setId('backend');
        $page5->setResource('adminpages');
        $page5->setOrder(5);

        $page5_1 = Zend_Navigation_Page::factory(array(
            'route' => 'backend',
            'params'=> array('controller' => 'category','action'=>'index'),
            'label' => 'Categories',
            'order' => 1
        ));
        
        $page5_2 = Zend_Navigation_Page::factory(array(
            'route' => 'backend',
            'params'=> array('controller' => 'itemgroup','action'=>'index'),
            'label' => 'ItemGroups',
            'order' => 2
        ));
        $page5_3 = Zend_Navigation_Page::factory(array(
            'route' => 'backend',
            'params'=> array('controller' => 'item','action'=>'index'),
            'label' => 'Items',
            'order' => 3
        ));
        $page5_4 = Zend_Navigation_Page::factory(array(
            'route' => 'backend',
            'params'=> array('controller' => 'init','action'=>'index'),
            'label' => 'Init',
            'order' => 4
        ));
        $page5_5 = Zend_Navigation_Page::factory(array(
            'route' => 'backend',
            'params'=> array('controller' => 'backup','action'=>'index'),
            'label' => 'Backup',
            'order' => 4
        ));
        
        $page4->addPages(array($page4_1,$page4_2));
        $page5->setPages(array($page5_1,$page5_2,$page5_3,$page5_4,$page5_5));
        $container->setPages(array($page1,$page2,$page2bis,$page3,$page4,$page5));
        
        Zend_Registry::set('Zend_Navigation',$container);
    }
}
    
   


