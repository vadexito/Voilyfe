<?php 

/**
 * @group Pepit
 */

require('/Pepit/Import/Data/Lieferando.php');

class PepitImportDataLiefrandoTest extends PHPUnit_Framework_TestCase
{
    protected $lieferando;

    public function setUp()
    {
        parent::setUp();
        
        $this->lieferando = new Pepit_Import_Data_Lieferando();
    }

        
    /**
     *
     * @dataProvider providerPrices 
     */
    public function testisPrice($price,$booleanInteger)
    {
        $this->assertEquals($booleanInteger,$this->lieferando->isPrice($price));
    }

    public function providerPrices()
    {
        return array(
            array('12 €',1),
            array('8,50 €',1),
            array('bla',0),
        );
    }
        
    
}


    



