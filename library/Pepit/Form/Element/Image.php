<?php

/**
 * @author DM
 * @package Mylife
 *  
 */

class Pepit_Form_Element_Image extends Zend_Form_Element_File 
    implements Pepit_Form_Element_Interface_Interface
{
    use Pepit_Form_Element_Trait_Trait, Pepit_Doctrine_Trait;
    
    protected $_target;
    
    protected $_keepExtension;
    
    public function init()
    {
        $this->setOptions(array(
            'horizontal'    => 'true'
        ));
        
        Pepit_Form_Element::initErrorHelper($this);
        Pepit_Form_Element::initDecoratorPath($this);
        $this->setDecorators(array('File'));
        if ($this->_horizontal === 'true')
        {
            $this->addDecorators(Pepit_Form_Element::getDecoratorsHorizontal());
            
        }
        else
        {
            $this->addDecorators(Pepit_Form_Element::getDecorators());
        }
        $this->removeDecorator('viewHelper');
        
        $this->addValidator('Count', false,1);
        $this->addValidator('Size', false, 5024000);
        $this->addValidator('Extension', false, 'jpg,png,gif');
        $this->setValueDisabled(true);
        $this->setLabel('item_image');
        
        parent::init();
    }
    
    public function mapElement($entity)
    {
        if ($this->getValue() === NULL)
        {
            return false;
        }
        
        //define adapter for renaming and saving file
        $adapter= new Zend_File_Transfer_Adapter_Http();
        
        //define new name dir file to be stored (config gives the path 
        //as from APPLICATION_PATH)
        $dir = Zend_Registry::get('config')->storage->images->members
                                            ->directoryPath;
        $extension = '.'.Pepit_File_Tool::getExtension($this->getValue());
        
        $image = new \ZC\Entity\Image();
        $this->getEntityManager()->persist($image);
        $this->getEntityManager()->flush($image);
        $id = $image->id;
        
        
        $imageName = $this->_getNewFileName(
                $id, 
                Zend_Auth::getInstance()->getIdentity()->id, 
                $extension
        );
        
        //storing the name in the storeage directory
        $image->path = $imageName;
        $this->getEntityManager()->flush($image);
        
        $adapter->addFilter('Rename',array('target' => APPLICATION_PATH.$dir.$imageName));
        
        //create file
        $adapter->receive();
        
        $property = $this->getAttrib('data-property-name');
        $entity->$property = $image;
    }
    
    public function populate($entity)
    {
        return false;
    }
    
    protected function _getNewFileName($imageId,$memberId,$fileExtension)
    {
        return 'img'.$imageId.'_member'.$memberId.$fileExtension;
    }
    
}
