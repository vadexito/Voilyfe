<?php

/**
 *
 * @group Entities 
 */

class EntityMemberTest extends Pepit_Test_DoctrineTestCase
{
    protected $properties;
    
    protected $propPerson;
    
    public function setUp()
    {
        parent::setUp();
        
        TestHelpersDoctrine::initUserRegisterItems($this->em);
        
        $hash = new Pepit_Auth_Hash;
        $salt = 'fgQ2jtpAIBAjX4fQl8MGoKMGHsFkUuRI';
        $country = $this->em->getRepository('ZC\Entity\ItemMulti\Country')->find(1);
        $language = $this->em->getRepository('ZC\Entity\ItemMulti\Language')->find(1);
        $this->properties = array(
            'firstName'         => 'firstName',
            'lastName'          => 'lastName',
            'userName'          => 'userName',
            'userPassword'      => $hash->hashPassword('pass', $salt),
            'passwordSalt'      => $salt,
            'email'             => 'first.last@somewhere.org',
            'registeringDate'   => new DateTime(),
            'role'              => 'owner',
            'country'           => $country,
            'language'          => $language
        );
    }
    
    
    public function testCanCreateMember()
    {
        $member = new ZC\Entity\Member();        
        
        
        foreach($this->properties as $property => $value)
        {
            $member->$property = $value;
        }
        
        $this->em->persist($member);
        $this->em->flush();
        $this->em->clear();
        
        $members = $this->em -> getRepository('ZC\Entity\Member')->findAll();
        $this->assertEquals(1,count($members));
        
        $memberDB = $members[0];
        
        $this->assertEquals($member->firstName,$memberDB->firstName);
        $this->assertEquals($member->lastName,$memberDB->lastName);
        $this->assertEquals($member->userName,$memberDB->userName);
        $this->assertEquals($member->userPassword,$memberDB->userPassword);
        $this->assertEquals($member->passwordSalt,$memberDB->passwordSalt);
        $this->assertEquals($member->email,$memberDB->email);
        $this->assertEquals($member->registeringDate,$memberDB->registeringDate);
        $this->assertEquals($member->role,$memberDB->role);
        $this->assertEquals($member->country->value,$memberDB->country->value);
        $this->assertEquals($member->language->value,$memberDB->language->value);
     
    }
    
    public function testCanRemoveMember()
    {
        $member = new ZC\Entity\Member();        
        
        
        foreach($this->properties as $property => $value)
        {
            $member->$property = $value;
            $this->assertEquals($value,$member->$property);
        }
        
        $this->em->persist($member);
        $this->em->flush();
        
        $this->em->remove($member);
        $this->em->flush();
        
        $members = $this->em -> getRepository('ZC\Entity\Member')->findAll();
        $this->assertEquals(0,count($members));
    }
}
