<?php
/**
 * Model Interface
 *
 * @package    Mylife
 * @author     DM
 */
interface Pepit_Model_Interface   
{
    /**
     * get data container
     *
     * @return object
     */
    public function getStorage();

    /**
     * insert data
     *
     * @param array $data Data to insert
     * @throws Pepit_Model_Exception 
     * @return string new key for inserted data
     */
    public function insert();
    
    /**
     * updating data
     *
     * @param array $data data to update
     * @param string $id key for data to update
     * @throws Pepit_Model_Exception 
     * @return boolean
     */
    public function update($id);
    
    /**
     * Delete data
     *
     * @param string $id key for data to delete
     * @throws Pepit_Model_Exception 
     * @return boolean
     */
    public function delete($id);
    
    /**
     * Get all data
     *
     * @return array
     */
    public function fetchEntries();
        
    /**
     * Get one line corresponding to primary unique key id
     *
     * @param string $id unique primary key
     * @return array
     */
    public function fetchEntry($id);
    
    
    
    
    
    
    
    
}
