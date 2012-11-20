<?php

class Backend_BackupController extends Zend_Controller_Action
{

    protected $_model;
    
    protected $_dirBackup;
    
    public function init()
    {
        
        $this->_model = new Backend_Model_Backup();
        $this->_dirBackup = APPLICATION_PATH.'/../data/backup_members/';
    }
    
    public function indexAction()
    {
        
    }
    
    public function savedataAction()
    {
        $this->_model->saveDataXMLAllMembers(
            $this->_dirBackup
        );
         
        $this->getHelper('flashMessenger')->addMessage(
                'Data successfully backed up in files'
        );
        
        $this->_redirect($this->view->url(array(
            'controller'    => 'index',
            'action'        => 'index',
        ),'backend'));
    }
    
    public function initdatabaseAction()
    {
        $path =  $this->_dirBackup.'member_id1.xml';
        $this->_model->initializeDataBaseFromXMLMemberBackup(
            $path
        );
         
        $this->getHelper('flashMessenger')->addMessage(
                'Database successfully updated form XML backup file.'
        );
        
        $this->_redirect($this->view->url(array(
            'controller'    => 'index',
            'action'        => 'index',
        ),'backend'));
    }


    
}











