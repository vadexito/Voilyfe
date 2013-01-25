<?php

abstract class Pepit_Model_Doctrine2 extends Pepit_Model_Abstract_Abstract implements Pepit_Model_Interface
{
    use Pepit_Model_Traits_BindForm, Pepit_Model_Traits_Doctrine2;
    
    protected $_userId = null;
    
    protected $_em = null;
    
    protected $_repository = null;
    
    protected $_storageName = null;
    
    protected $_flushOnInsert = true;
    
    const FORM_ENTRY_SEPARATOR = ',';

    static public function updateDoctrineDataBase()
    {
        $tool = new Doctrine\ORM\Tools\SchemaTool($this ->getEntityManager());
        $tool->updateSchema($this ->getEntityManager()->getMetadataFactory()->getAllMetadata());
    }
    
    static public function ResultToArrayByKey(array $data,$key)
    {
        $newArray = array();
        foreach($data as $value)
        {
            $newArray[] = $value[$key];
        }
        return $newArray;
    }
    
    /**
     * hydrate an entity with the properties contained in an array
     * @param Doctrine\ORM\Mapping\Entity $entity
     * @param array $properties
     * @param type $persist
     * @return \Doctrine\ORM\Mapping\Entity 
     */
    static public function hydrateEntityFromArray($entity,Array $properties)
    {
        foreach($properties as $property => $value)
        {
            $entity->$property = $value;
        }
        return $entity;
    }
    
    /**
     * returns a set of row corresponding to the userid items
     * 
     * @return Zend_Db_Table_Rowset 
     */
    
    public function __construct($em = NULL)
    {
        //initialize user's id if logged in
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->_userId = $user->id;
        }
        
        //initialize entity manager
        if ($em === NULL)
        {
            $em = Zend_Registry::get('entitymanager');
        }
        $this->_em = $em;
        
        //initialize repository
        $this->_repository = self::loadStorage($this->_storageName);
        
    }
    
    
    
    /**
     * insert an element in the database through doctrine2
     * @return integer newid
     * @throws Pepit_Model_Exception 
     */
    
    public function insert()
    {
        try
        {
            // create new user from array
            $entity = $this->createEntityFromForm();

            // Save new entity
            $this ->getEntityManager()->persist($entity);
            $this ->getEntityManager()->flush($entity);

        } 
        catch (Exception $e)
        {
            throw new Pepit_Model_Exception('Insert Not Possible because : '
                    .$e->getMessage());
        }
        // return new id if it worked
        return $entity->id;        
    }
    
      /**
     * update data
     * @param array $data
     * @param integer $id : the primary index
     * @return boolean : true if it is ok
     */
    
    
    public function update($entityId)
    {
       try
       {
            // update data base
            $this->updateEntityFromForm($entityId);
            
            // flush for updating new entity
            $this->getEntityManager()->flush();
            
        } 
        catch (Exception $e)
        {
            
            throw new Pepit_Model_Exception('Update Not Possible because of '
                    .$e->getMessage());
        }
        return true; 
    }
    
    /**
     * delete en entity in the table
     * 
     * @param integer id
     * @return true if it was ok
     */
    
    public function delete($entityId)
    {
        try
        {
            // find row to delete with id
            $this ->getEntityManager()->remove($this->getStorage()->find($entityId));
            $this ->getEntityManager()->flush();
            
        }
        catch (Exception $e)
        {
            throw new Pepit_Model_Exception('Delete Not Possible because of '
                    .$e->getMessage());
        }
        // return true if it is done
        return true;
    }
    
   
    /**
     * create and update a new entity from data comming from Form
     *  
     */
    
    abstract public function createEntityFromForm();    
    
    public function updateEntityFromForm ($memberId)
    {
        return $this->_saveEntityFromForm($this->getStorage()->find($memberId));
    }
    
    
    /**
     * hydrate the property array collection (assciation XToMany)
     * @param Doctrine Entity $entity
     * @param string $keyPluralName
     * @param array $valueIds
     * @return Doctrine Entity 
     */
    public function hydrateArrayCollectionFromEntity(
                                $entity,$keyPluralName,$valueIds,$targetEntity)
    {
        if (!$valueIds)
        {
            return $entity;
        }
        if (is_array($valueIds))
        {
            foreach ($valueIds as $entityId)
            {
                $keySg = Pepit_Inflector::singularize($keyPluralName);
                $addMethod = 'add'.ucfirst($keySg);
                $entity->$addMethod(
                        $this ->getEntityManager()->getRepository($targetEntity)
                             ->find($entityId)
                );
            }
        }
        elseif ($valueIds)
        {
            throw new Pepit_Model_Exception(
                    'The format of values for array collection should be an array'
            );
        }
        return $entity;
    }
   
    
    public function fetchAllIdNameArray()
    {
        $valuesDB = $this->_repository->findAll();
        
        $values = array();
        foreach($valuesDB as $value)
        {
            $values[$value->id] = $value->name;
        }
        
        return $values;
    }
    
   
    
    /**
     * fetch unique entry with primary key given (can be an array)
     * 
     * @param string | array $id
     * @return array 
     */
    
    public function fetchEntry($id)
    {
        $row = $this->getStorage()->find($id);
        
        return $row;
    }
    
    
    public function fetchEntries()
    {
        return $this->getStorage()->findAll();
    }
    
   
    
    /**
    * calculates the average number of row per given period in a given period
    * @param type $rowset
    * @param type $period_begin
    * @param type $period_length
    * @param type $period_base
    * @return type 
    */
    
    
    public function stat()
    {
        $stat = array(
          'lastWeek'        => count($this->fetchEntriesByDate('NOW() - INTERVAL 1 WEEK')),
          'lastMonth'       => count($this->fetchEntriesByDate('NOW() - INTERVAL 1 MONTH')),
          'lastYear'        => count($this->fetchEntriesByDate('NOW() - INTERVAL 1 YEAR')),
          'AvWeekLast6M'    => count($this->fetchEntriesByDate('NOW() - INTERVAL 6 MONTH')) / (52/2),
          'AvMonthLastY'    => count($this->fetchEntriesByDate('NOW() - INTERVAL 1 WEEK')) / 12,
          'AvYearLast10Y'   => count($this->fetchEntriesByDate('NOW() - INTERVAL 1 WEEK')) / 10
        );
        return $stat;
    }
    
   
    
    
    public function getEntityManager()
    {
        if ($this->_em === null)
        {
            $this->_em = Zend_Registry::get('entitymanager');
        }
        return $this->_em;
    }
    /**
     * hook function to be overriden to add elements
     * @param DoctrineEntity $entity
     * @return array
     */
    public function getArrayForFormUpdateFromEntity($entityId)
    {
        $entity = $this->getStorage()->find($entityId);
        
        $arrayResult = [];
        foreach ($this->getForm()->getElements() as $formElement)
        {
            if (method_exists($formElement,'populate'))
            {
               $arrayResult[$formElement->getId()] = $formElement->populate(
                    $entity
                );                
            }
        }
        
        return $arrayResult;
    }
    
    /**
     * work only with single items with unique or many one-value field(s) 
     * with one field item::value
     * 
     * @param doctrine entity $entity
     * @param array $FormElementKeysSet has to be the name of the key in the 
     * form (not the name of the item)
     * @return type 
     */
    
    static public function getArrayForFormFromEntityWithSingleValueFields($em,
                                                    $entity,$formElementKeysSet)
    {
        $classMetadata = $em->getMetadataFactory()
                            ->getMetadataFor(get_class($entity));
        //get single variable and association variable datas
        $fields = array_keys($classMetadata->fieldMappings);
        $associationMappings = $classMetadata->associationMappings;
        
        //@ML-TODO put into associationType classes
        $arrayForForm = array();
        foreach ($formElementKeysSet as $formElementKey)
        {
            //case of single value (mapfield)
            if (in_array($formElementKey,$fields))
            {
                $arrayForForm[$formElementKey] = $entity->$formElementKey;
            }
            else
            // case of an association value    
            {
                $property = preg_replace('#(.*)Ids?$#','$1',$formElementKey);
                if (preg_match('#Ids$#',$formElementKey))
                {
                    $property = Pepit_Inflector::pluralize($property);
                }
                if (in_array($property,array_keys($associationMappings)))
                {
                    //get the type of association
                    $type = $associationMappings[$property]['type'];
                    
                    //case of a many to one association 
                    //the key in the form is key.'Id', form element 
                    //is select or checkbox
                    if (($type == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE)&&
                        (property_exists($entity,$property)) && 
                            ($entity->$property))

                    {
                        $arrayForForm[$property.'Id'] = $entity->$property->id;
                    }

                    //in case of a many to many 
                    //the key in the form is key.'Ids', form element 
                    //is multiselect or multicheckbox
                    if (($type == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY) &&
                        property_exists($entity,$property)&&
                        ($entity->$property))
                    {
                        $sgProp = Pepit_Inflector::singularize($property);
                        $arrayForForm[$sgProp.'Ids'] = array();
                        foreach($entity->$property as $value)
                        {
                            $arrayForForm[$sgProp.'Ids'][] = $value->id;
                        }
                    }
                    //in case of one to Many association with event
                    // form element is text
                    if (($type == \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY) &&
                        property_exists($entity,$property)&&
                        ($entity->$property != NULL))
                    {
                        $values = array();
                        foreach ($entity->$property as $formOption)
                        {
                            $values[] = $formOption->value;
                        }
                        $arrayForForm[$property] = implode(
                            self::FORM_ENTRY_SEPARATOR,
                            $values
                        );
                    }
                    //in case of one to Many association with event
                    // form element is text
                    if (($type ==\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE) &&
                        property_exists($entity,$property)&&
                        ($entity->$property != NULL))
                    {
                        $arrayForForm[$property] = $entity->$property;
                    }
                }
            }
        }
        return $arrayForForm;
    }   
    
    /**
     * 
     * @param mixed $entity the property names for the entity must correspond
     * to the name of the formElement
     * @return mixed
     */
    protected function _saveEntityFromForm($entity)
    {
        if ($this->getForm())
        {
            foreach ($this->getForm()->getValues() as $formElementName => $value)
            {
                if (method_exists($this->getForm()->getElement($formElementName),'mapElement'))
                {
                    $this->getForm()->getElement($formElementName)->mapElement($entity);
                }
                else
                {
                    throw new Pepit_Model_Exception('A mapElement function must be define for element : '
                            .get_class($this->getForm()->getElement($formElementName)));
                }
               
            }
            
            return $entity;
        }
        else
        {
            throw new Pepit_Model_Exception('The model must be bound with a form. No form registered on the entity '
                .  get_class($entity));
        }
    }
    
    public function addMember($entity)
    {
        if (is_object($entity) && property_exists($entity,'member'))
        {
            $entity->member = $this->getEntityManager()
                        ->getRepository('ZC\Entity\Member')
                        ->find(Zend_Auth::getInstance()->getIdentity()->id);
        }
        else
        {
            throw new Pepit_Model_Exception('Addmember requires an entity doctrine or entity has no property member');
        }
        
        
    }
    
}
