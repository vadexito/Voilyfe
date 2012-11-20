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
    
    public function updateEntityFromForm(Array $formValues,$memberId)
    {
        $member = $this->_repository->find($memberId);
        
        return $this->_saveEntityFromForm($formValues, $member);
    }
    
    protected function _saveEntityFromForm($member)
    {
        $formValues = $this->getForm()->getValues();
        
        $member->modificationDate = new \DateTime();
        
        $validateNotEmpty = new Zend_Validate_EmailAddress();
        if ($validateNotEmpty->isValid($formValues['email']))
        {
            $member->email = $formValues['email'];
        }
        else
        {
            throw new Pepit_Model_Exception('Invalid email address');
        }
        
        //can be null
        if (array_key_exists('firstName',$formValues))
        {
            $member->firstName = $formValues['firstName'];
        }
        if (array_key_exists('lastName',$formValues))
        {
            $member->lastName = $formValues['lastName'];
        }
        if (array_key_exists('languageId',$formValues) && 
                                       (int)$formValues['languageId'] > 0)
        {
             $member->language = $this->_em
                            ->getRepository('ZC\Entity\ItemMulti\Language')
                            ->find($formValues['languageId']);
             if ($member->language === NULL)
             {
                 throw new Pepit_Model_Exception('No language corresponding to id in the database');
             }
        }
        
        if (array_key_exists('countryId',$formValues) && 
                                        (int)$formValues['countryId'] > 0)
        {
            $member->country = $this->_em
                            ->getRepository('ZC\Entity\ItemMulti\Country')
                            ->find($formValues['countryId']);
            if ($member->country === NULL)
             {
                 throw new Pepit_Model_Exception('No country corresponding to id equal to '.$formValues['countryId'].' in the database');
             }
        }
        
        return $member;
    }
}

