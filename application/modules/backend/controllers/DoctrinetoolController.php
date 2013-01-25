<?php

class Backend_DoctrinetoolController extends Zend_Controller_Action
{
    use Pepit_Model_Traits_Doctrine2;
    
    protected $_tool;   
    protected $_em;


    public function init()
    {
        $this->_em = Zend_Registry::get('entitymanager');
        $this->_tool = new Doctrine\ORM\Tools\SchemaTool($this->_em);        
    }
    
    public function updatedatabaseAction()
    {
        $form = new Backend_Form_UpdateDoctrineDB;
        $form->setAction($this->view->url(
                ['controller' => 'doctrinetool','action' => 'updatedatabase'],
                'backend'                
        ));
        
        if ($this->getRequest()->isPost() || $this->getRequest()->isGet() )
        {
            // get row from post
            $userInput = $this->getRequest()->getParams();
            $valid = $form->isValid($userInput);
            $submitChecked = $form->getElement('submit_update')->isChecked();
            
            // check if data are ok
            if ($valid && $submitChecked) 
            {
                $this->_tool->updateSchema($this->_em->getMetadataFactory()->getAllMetadata());        
                return $this->_redirect($this->view->url(
                    ['controller'=> 'category','action'=> 'index'],
                    'backend'));
                
            }
        }
        
        $this->view->sql =$this->_tool->getUpdateSchemaSql($this->_em->getMetadataFactory()->getAllMetadata());
        $this->view->form = $form;
    }
}











