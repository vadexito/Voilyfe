<?php

class Events_View_Helper_VisualRep extends Zend_View_Helper_Abstract
{
    
    protected $options;
    protected $_formElement;
    
    public function visualRep(Zend_Form_Element $formElement,$events,$options = NULL)
    {
        $this->_formElement = $formElement;
        if (method_exists($formElement,'dataChart'))
        {
            $dataChart = $formElement->dataChart($events);
            switch($dataChart['type'])
            {
                case 'google_chart' : 
                   return $this->_getGoogleChartHtml($options,$dataChart);
            }
        }
        
        return '';
        
    }
    
    protected function _getGoogleChartHtml($options,$dataChart)
    {
        if (!$options)
            {
                $this->loadDefaultOptions();
            }

            $htmlTag = $this->options['htmlTag'];
        
            return 
            '<'.$htmlTag." id='".$this->getId()."' class='visual-rep' data-visual='"
            .  Zend_Json::encode($dataChart['data'])
            ."'></".$htmlTag.'>'."\n";
    }
    
    public function loadDefaultOptions()
    {
        $this->options = [
            'htmlTag'   => 'div',
            'id'        => 'visual_'. $this->_formElement->getId()
        ];
        
        return $this;
    }
    
    public function getId()
    {
        return $this->options['id'];
    }
    
}
