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
        'login'  => 'userLogin',
        'logout' => 'userLogout',
        'delete' => 'userDelete'
    );
    
    /**
     * create entity member from array data comming from create Form
     * @param array $data
     * @return \ZC\Entity\Member 
     */
    
    public function createEntityFromForm() 
    {
        $formValues = $this->getForm()->getValues();

        //create new member
        $member = new \ZC\Entity\Member();
        
        //add general properties
        $member->registeringDate = new \DateTime();
        $member->role = 'member';
        
        $hashFactory = new Pepit_Auth_Hash();
        $member->passwordSalt = $hashFactory->getSalt();
        $member->userPassword = $hashFactory->hashPassword(
            $formValues['userPassword'],
            $member->passwordSalt
        );
        
        $validateNotEmpty = new Zend_Validate_NotEmpty();
        if ($validateNotEmpty->isValid($formValues['userName']))
        {
            $member->userName = $formValues['userName'];
        }
        else
        {
            throw new Pepit_Model_Exception('Invalid user name');
        }
        
        return $this->_saveEntityFromForm($member);
    }
    
    protected function _saveEntityFromForm($member)
    {
        $member->modificationDate = new \DateTime();
        
        parent::_saveEntityFromForm($member);
        
        return $member;
    }
}

