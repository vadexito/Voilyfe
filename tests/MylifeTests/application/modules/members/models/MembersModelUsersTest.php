<?php

/**
 * @group Models 
 * @group Members
 */

class MembersModelUsersTest extends Pepit_Test_DoctrineTestCase
{
    protected $_repository;
    
    protected $_model;
    
    
    public function setUp()
    {
        parent::setUp();
        
        $this->_repository = $this->em->getRepository('ZC\Entity\Member');
        
        $this->_model = new Members_Model_Users($this->em);
        
        TestHelpersDoctrine::initUserRegisterItems($this->em);
    }
    
    /**
     *
     * @dataProvider providerCreateMemberSuccess 
     */
    
    public function testCreateEntityNewMember($initialData)
    {
        
        //insert new $user
        $member = $this->_model->createEntityFromForm($initialData);
        $salt = $member->passwordSalt;
        $this->em->persist($member);
        $this->em->flush();
        
        if (array_key_exists('countryId', $initialData))
        {
            $country = $this->em ->getRepository('ZC\Entity\ItemMulti\Country')
                             ->find($initialData['countryId']);
        }
        if (array_key_exists('languageId', $initialData))
        {
            $language = $this->em ->getRepository('ZC\Entity\ItemMulti\Language')
                              ->find($initialData['languageId']);
        }                              
        
        
        $hash = new Pepit_Auth_Hash;
        
        foreach ($initialData as $key => $value)
        {
            switch ($key)
            {
                case 'userPassword' :
                    $this->assertEquals(
                        $hash->hashPassword($value, $salt),
                        $member->$key
                    );
                    break;
                case 'countryId' : 
                    $this->assertEquals($country->value,$member->country->value);
                    break;
                default :
                case 'languageId' : 
                    $this->assertEquals($language->value,$member->language->value);
                    break;
                case 'confirmPassword' :
                    break;
                default :
                    $this->assertEquals($value,$member->$key);
                    break;
            }
            $this->assertEquals('member',$member->role);
        }
        
        //test the date field
        $this->assertInstanceOf('\DateTime',$member->registeringDate);
    }
    
    public function providerCreateMemberSuccess ()
    {
        return array(
            array(array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
            //no first and lastname
            array(array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
            //no confirm password
            array(array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
            //no country or language id
            array(array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            )),
        );
    }


    /**
     *  test error inserting
     *  @expectedException Pepit_Model_Exception
     *  @dataProvider providerInsertFailures
     */
    public function testInsertMemberThrowingException($fixture)
    {
        //insert new $user
        $this->_model->insert($fixture);
    }
    
    public function providerInsertFailures()
    {
        return array(
            array(array(
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
            array(array(
            'userName'                  => 'gooduserName',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 100,
//            'languageId'                => 1
            )),
            array(array(
            'userName'                  => 'gooduserName',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
            array(array(
            'userName'                  => 'gooduserName',
            'email'                     => 'luigi@mario.it',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
            array(array(
            'userName'                  => '',
            'email'                     => 'luigi@mario.it',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1
            )),
        );
    }
}