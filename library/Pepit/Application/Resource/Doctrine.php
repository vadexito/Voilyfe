<?php

/**
 *
 * Pepit_Resources_Doctrine
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{

    const DEFAULT_REGISTRY_KEY = 'entitymanager';
    
    public function init()
    {
        return $this->initDoctrineAndGetEntityManager();
    }
    
    public function initDoctrineAndGetEntityManager()
    {
        $doctrineConfig = $this->getOptions();
        
        //define class loader
        require_once('Doctrine/Common/ClassLoader.php');
        $classLoader = new \Doctrine\Common\ClassLoader(
            'Doctrine', 
            APPLICATION_PATH . '/../library/'
        );
        $classLoader->register();

        
        // create the Doctrine configuration
        $config = new \Doctrine\ORM\Configuration();

        // setting the cache
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        
        
        //@ML-TODO implement apccache metadata driver for production
        
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        
        //metadata driver
        $configData = $doctrineConfig['dbal']['config']['parameters'];
        $chaindriver = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        
        $mainDriver = new Doctrine\ORM\Mapping\Driver\StaticPHPDriver(
            $configData['metadatadriver']['path']
        );        
        $chaindriver->addDriver($mainDriver,'ZC\Entity');
        
        $config->setMetadataDriverImpl($chaindriver);
        
        //proxies      
        $config->setProxyDir($configData['proxies']['dir']['path']);
        $config->setAutoGenerateProxyClasses((
                (APPLICATION_ENV == 'development') ||
                (APPLICATION_ENV == 'testing')
        ));
        $config->setProxyNamespace($configData['proxies']['namespace']);
        
        // create the entity manager and use the connection
        // settings we defined in our application.ini
        $connectionSettings = $doctrineConfig['dbal']['connection']['parameters'];
        $conn = array( 
            'driver'    => $connectionSettings['driv'],
            'user'      => $connectionSettings['user'],
            'password'  => $connectionSettings['pass'],
            'dbname'    => $connectionSettings['dbname'],
            'host'      => $connectionSettings['host'],
            'charset'   => 'utf8',
            'driverOptions' => array(1002 =>'SET NAMES utf8')
        );
        $entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);
        $entityManager->getEventManager()
                      ->addEventSubscriber(
                  new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit(
                          'utf8',
                          'utf8_unicode_ci'
                ));
        
        //activate SQL logger (debug tool)
        if (key_exists('sqlLoggerClass',$connectionSettings))
        {
            $logger = $connectionSettings['sqlLoggerClass'];
            $entityManager->getConnection()->getConfiguration()
                ->setSQLLogger( new $logger());
        }
        
        // push the entity manager into our registry for later use
        Zend_Registry::getInstance()
                ->set(self::DEFAULT_REGISTRY_KEY,$entityManager);
        
        return $entityManager;
    }

}

