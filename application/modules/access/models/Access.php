<?php

class Access_Model_Access extends Pepit_Model_Abstract_Abstract 
{
    use Pepit_Model_Traits_BindForm;
    
    /**
     * entity name
     * @var array
     */
    protected $_storageName = 'ZC\Entity\Member';
    
    protected $_em;
    
    protected $_formClasses = ['login'  => 'userLogin'];
    
    public function __construct()
    {
        $this->_em = Zend_Registry::get('entitymanager');
    }
    
    public function getEntityManager()
    {
        return $this->_em;
    }


    /**
     * process login
     * 
     * @return boolean 
     *
     */
    public function processLogin($adapter = NULL)
    {
        if ($adapter === NULL)
        {
            $adapter = $this->_getAuthAdapter();
        }
        $userName = $this->getForm()->getValue('userName');
        $adapter->setIdentity($userName);
        
        //get salt if there is any
        if ($this->getStorage()->findByUserName($userName))
        {
            $hashedFactory = new Pepit_Auth_Hash();
            $salt = $this->getStorage()->findOneByUserName($userName)->passwordSalt;
            $hashedPassToCheck = $hashedFactory->hashPassword(
            $this->getForm()->getValue('userPassword'),
            $salt
             );
        }
        else
        {
            $hashedPassToCheck='noUserNameFound';
        }
        
        $adapter->setCredential($hashedPassToCheck);
        $result = Zend_Auth::getInstance()->authenticate($adapter);

        //check if login is ok
        if($result->isValid())
        {
            //the user is stored in a auth storage object
            Zend_Auth::getInstance()->getStorage()->write(
                    $adapter->getResultRowObject(['userName','id','role']));
          //if login is successful 
           return true;
        } 
        return false;
    }

    /**
     * get the adapter for login authentification
     * @return \Zend_Auth_Adapter_DbTable 
     *
     */
    protected function _getAuthAdapter()
    {
        return new Pepit_Auth_Adapter_Doctrine2(
                $this->_em,
                'ZC\Entity\Member',
                'userName',
                'userPassword'
        );    
    }
    
    public function getStorage()
    {
        return $this->_em->getRepository($this->_storageName);
    }
}

