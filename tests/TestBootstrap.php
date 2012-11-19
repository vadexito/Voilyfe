<?php
// start stamp memory
ob_start();

//maximize memory limit
ini_set('memory_limit', -1);

// Choose application constants
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'testing');

// Error report activated, no error when on production server
error_reporting(E_ALL | E_STRICT);


// Ensure library/ and tests/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../tests'),
    get_include_path(),
)));

/** Zend_autoload */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Application_');
$autoloader->registerNamespace('Pepit_');

require_once 'Helpers/TestHelpersDoctrine.php';
require_once('PHP/Token/Stream/Autoload.php');

//register doctrine autoloader
require_once('Doctrine/Common/ClassLoader.php');
$classLoader = new \Doctrine\Common\ClassLoader(
    'Doctrine', 
    APPLICATION_PATH . '/../library/'
);
$classLoader->register();

//mock server
$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

unset($autoloader);