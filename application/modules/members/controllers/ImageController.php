<?php

class Members_ImageController extends Pepit_Controller_Abstract_Abstract
{
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $resource = new Application_Acl_WithOwnerResource(
                'members:image',
                Zend_Auth::getInstance()->getIdentity()->id 
        );
        $this->_helper->aclAuthor($resource);
        
    }
    
    public function init()
    {
        parent::init();
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function showAction()
    {
        $imagePath = $this->getRequest()->getParam('image');
        $dirPath = Zend_Registry::get('config') ->storage
                                                ->images->members
                                                ->directoryPath;
        $image = file_get_contents(APPLICATION_PATH.$dirPath.$imagePath);
        $extension = Pepit_File_Tool::getExtension($imagePath);
        $authorizedExtension=['jpeg' => ['jpeg','jpg'],'png' => ['png'],'gif' => ['gif']];
        foreach ($authorizedExtension as $extMime => $extensions)
        {
            if (in_array($extension,$extensions))
            {
                $this->getResponse()->clearBody ();
                $this->getResponse()->clearAllHeaders();
                $this->getResponse()->setHeader('Content-Type', 'image/'.$extMime,true);
                $this->getResponse()->setHeader('Cache-Control', 'public');
                $this->getResponse()->setBody($image);
                
            }
        }
        
    }
    
}

