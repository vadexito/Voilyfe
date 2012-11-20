<?php

/**
 * form for Event creation
 * 
 * Author : DM 
 */



class Events_Form_EventCreate extends Events_Form_Abstract_GeneralizedItemRowCreate
{
    
    public function init()
    {
        $this->addAttribs(array(
            'id' => 'add_event',
            'class' => 'form-horizontal'
        ));
        
        //date element
        $this->addElements(array(
                new Events_Form_Elements_Date('date'),
                new Events_Form_Elements_Location('location'),
                new Events_Form_Elements_Persons('persons'),
                new Events_Form_Elements_Tags('tags'),
                $this->_inputFileIsSupported() ? new Events_Form_Elements_Image('image') : null
        ));
        
        parent::init();
    }
    
    protected function _inputFileIsSupported()
    {
        $wurflCapabilities = Zend_Registry::get('capabilities');
        if ($wurflCapabilities['xhtml_file_upload'] === 'supported')
        {
            return true;
        }
        if ($wurflCapabilities['is_wireless_device'] === 'true' &&
            $wurflCapabilities['is_tablet'] === 'false')
        {
            return true;
        }
        return false;
    }
}

