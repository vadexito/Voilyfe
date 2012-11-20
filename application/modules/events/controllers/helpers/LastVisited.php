<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Events_Controller_Action_Helper_LastVisited 
    extends Zend_Controller_Action_Helper_Abstract
{

    protected $_session;
    
    public function __construct()
    {
        $this->_session = Zend_Registry::get('session_history_events');
                
        if (!is_array($this->_session->last))
        {
            $this->_session->last = array();
        }
    }

    public function addLastVisited($url)
    {
        if (!in_array($url,$this->_session->last))
        {
            $this->_session->last[] = $url;
        }
    }
    
    public function getLastVisited()
    {
        if ($this->hasLastVisited())
        {
            return end($this->_session->last);
        }
        else
        {
            return null;
        }
    }
    
    public function hasLastVisited()
    {
        return (is_array($this->_session->last) &&
                count($this->_session->last) > 0);
    }
    
    public function resetLastVisited()
    {
        end($this->_session->last);
        $last = each($this->_session->last);
        unset($this->_session->last[$last[0]]);
    }
    
    public function reset()
    {
         $this->_session->last = array();
    }
    
    public function redirectUrl($url)
    {
        if ($this->hasLastVisited())
        {
            return $this->getLastVisited();
        }
        return $url;
    }

}

