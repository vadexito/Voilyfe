<?php

class Members_Model_Users extends Pepit_Model_Doctrine2
{

    /**
     * entity name
     * @var array
     */
    protected $_storageName = 'ZC\Entity\Member';
    
    protected $_formClasses = array(
        'insert' => 'userRegister',
        'update' => 'userUpdate',
        'delete' => 'userDelete'
    );
    
    /**
     * create entity member from array data comming from create Form
     * @param array $data
     * @return \ZC\Entity\Member 
     */
    
    public function createEntityFromForm() 
    {
        $this->getForm()->removeElement('confirmPassword');
        $this->getForm()->removeElement('captcha');
        
        //create new member
        $member = new \ZC\Entity\Member();
        
        //add general properties
        $member->registeringDate = new \DateTime();
        $member->role = 'member';
        
        return $this->_saveEntityFromForm($member);
    }
    
    protected function _saveEntityFromForm($member)
    {
        $member->modificationDate = new \DateTime();
        parent::_saveEntityFromForm($member);
        
        return $member;
    }
}

