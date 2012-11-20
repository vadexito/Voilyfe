<?php

/**
* 
* creates a view for events
* 
* 
*/  

abstract class Pepit_View_Helper_EventTable_Abstract extends Zend_View_Helper_Abstract
{
    protected $_headTh = '';
    
    protected $_bodyTd = '';
    
    protected $_body = '';
    
    protected $_tableClass = NULL;
    
    protected $_tableClassDefault = 'table table-striped table-hover table-condensed';

    public function getView()
    {
        return $this->view;
    }
    
    public function getTableClass()
    {
        if (!$this->_tableClass)
        {
            $this->_tableClass = $this->_tableClassDefault;
        }
        return $this->_tableClass;
    }
    
    public function setOptions($options)
    {
        if (is_array($options) && $options['tableClass'])
        {
            $this->_tableClass = $options['tableClass'];
        }
    }
    
    
    public function getTable()
    {
        $tableClass = $this->getTableClass();
        
        return '<table class="'.$tableClass.'">
            <thead>'
                .$this->getTableHead().
            '</thead>
            <tbody>'.$this->getTableBody().'</tbody>
            </table>';
    }
    
    abstract public function setTableHead($events);
    abstract public function setTableBody($events);
    
    public function getIcons($categoryId,$eventId)
    {
        $linkEdit = '<a href="'
            .$this->getView()->url(
                    array(
                        'action' => 'edit',
                        'containerId'=>$categoryId,
                        'containerRowId'=>$eventId),
                    'event'
            )
            .'" title ="'
            .$this->getView()->translate('action_edit')
            .'" class="btn btn-small"><i class="icon-edit"></i> </a>'."\n"; 
        
        $linkDelete = '<a href="'
            .$this->getView()->url(
                    array(
                        'action' => 'delete',
                        'containerId'=>$categoryId,
                        'containerRowId'=>$eventId
                        ),
                    'event'
            )
            .'" title ="'
            .$this->getView()->translate('action_delete')
            .'" class="btn btn-small"><i class="icon-remove"></i> </a>'."\n"; 
        
        return $linkEdit.$linkDelete;
    }
    
    public function addHeadTh($th)
    {
        $this->_headTh='<th>'.$th.'</th>'."\n".$this->_headTh;
    }
    
    public function getTableHead()
    {
        return '<tr>'.$this->_headTh.'</tr>';
    }
    
    public function addTdToTr($td,$tr)
    {
        return $tr ='<td>'.$td.'</td>'."\n".$tr;
    }
    
    public function addBodyTr($tr)
    {
        $this->_body.= '<tr>'.$tr.'</tr>'."\n";
    }
    
    public function getTableBody()
    {
        return $this->_body;
    }
    
}
