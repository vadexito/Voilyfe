<?php 

class Events_RestController extends Zend_Rest_Controller
{
    
    public function init()
    {
        parent::init();
        
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch  ->addActionContext('index', array('json'))
                        ->addActionContext('get', array("json"))
                        ->addActionContext('post', array("json"))
                        ->addActionContext('put', array("json"))
                        ->addActionContext('delete', array("json"))
                        ->initContext('json');
    }

    // Handle GET and return a list of resources
    public function indexAction()
    {
        $this->view->response = array('key' => 'value of all');
    }

    
    public function headAction()
    {
        
    }
    
    // Handle GET and return a specific resource item
    public function getAction()
    {
        $this->view->response = array('key' => 'value of one');
    }

    // Handle POST requests to create a new resource item
    public function postAction()
    {
        $this->view->response = array('key' => 'create new thing');
    }

    // Handle PUT requests to update a specific resource item
    public function putAction()
    {
        $this->view->response = array('key' => 'update new thing');
    }

    // Handle DELETE requests to delete a specific item
    public function deleteAction()
    {
        $this->view->response = array('key' => 'delete new thing');
    }
}

