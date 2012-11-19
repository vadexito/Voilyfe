<?php

require_once APPLICATION_PATH.
    '/../tests/MylifeTests/application/modules/events/controllers/GeneralizedContainerRowTestAbtract.php';
/**
 * @group Controllers
 * @group Events
 * 
 */
class EventControllerTest extends GeneralizedContainerRowTestAbtract
{
    static protected $names;


    static public function setUpBeforeClass()
    {
        self::$containerRowType = 'event';
        self::$containerType = 'category';
        self::$containerRowEntity = 'ZC\Entity\Event';
        self::$containerRowModel = 'Events_Model_Events';
        self::$containerModel = 'Backend_Model_Categories';
        
        parent::setUpBeforeClass();
    }
}
