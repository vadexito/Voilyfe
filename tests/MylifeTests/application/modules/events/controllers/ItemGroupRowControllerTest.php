<?php

require_once APPLICATION_PATH.
    '/../tests/MylifeTests/application/modules/events/controllers/GeneralizedContainerRowTestAbtract.php';
/**
 * @group Controllers
 * @group Events
 * 
 */
class ItemGroupRowControllerTest extends GeneralizedContainerRowTestAbtract
{
    static public function setUpBeforeClass()
    {
        self::$containerRowType = 'itemGroupRow';
        self::$containerType = 'itemGroup';
        self::$containerRowEntity = 'ZC\Entity\ItemGroupRow';
        self::$containerRowModel = 'Events_Model_ItemGroupRows';
        self::$containerModel = 'Backend_Model_ItemGroups';
        
        parent::setUpBeforeClass();
    }
}
