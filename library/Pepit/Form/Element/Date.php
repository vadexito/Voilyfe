<?php

/**
 * define an element text for crete item form
 * 
 *  
 */

class Pepit_Form_Element_Date extends Pepit_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formText';
    
    public $_helperMobile = 'formDate';
    
    public function init()
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        if (isset($session->deviceType) && 
            ($session->deviceType === Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE))
        {
            $this->helper = $this->_helperMobile;
        }
        else
        {
            $this->setAttrib('class','datepickerAddEvent');
        }
        
        $validatorDate = new Zend_Validate_Date(array('format' => 'yyyy-M-dd'));
        
        $this->setOptions(array(
            'label'     => 'item_date',
            'required'  => 'true',
            'filters'   => array('StringTrim'),
            'validators'=> array($validatorDate),
            'horizontal'=> true,
        ));
        
        parent::init();
    }
    
    public function mapElement($entity)
    {
        $property = $this->getAttrib('data-property-name');
        $entity->$property = (new Pepit_Filter_DateToDateTime())->filter(
            $this->getValue(),
            Pepit_Date::MYSQL_DATE
        );
        
        return true;
    }
    
    public function dataChart($events)
    {
        $options = [
            'title' => ucfirst($this->getTranslator()->translate('item_frequency')),
            'type' => 'frequency',
            'periodNb' => 6,
            'timeUnitNb' => 1,
            'timeUnit' => 'month',
            'hAxisTitle' => ucfirst($this->getTranslator()->translate('unit_month')),
        ];
        
        return [
            'type' => 'google_chart',
            'data' => (new Pepit_Widget_Chart($events,$options))->dataForGoogleCharts()
        ];
    }
    
    
    public function populate($entity)
    {
        $filter = new Pepit_Filter_DateTimeToDateForm(array(
            'date_format' => Pepit_Date::MYSQL_DATE
        ));
        if (property_exists($entity,'date'))
        {  
            return $filter->filter($entity->date);
        }
        return false;
    }
}
