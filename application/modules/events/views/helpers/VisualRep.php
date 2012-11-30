<?php

class Events_View_Helper_VisualRep extends Zend_View_Helper_Abstract
{
  
    const VISUAL_TYPE_GOOGLE_CHART = 'google_chart';
    const VISUAL_TYPE_WINNER_LIST = 'winner_list';
    
    
    protected $options;
    protected $_formElement;
    protected $_id = NULL;
    
    public function visualRep(Zend_Form_Element $formElement,$events,$options = NULL)
    {
        $this->_formElement = $formElement;
        
        if (is_array($options) && key_exists('id',$options))
        {
            $this->setId($options['id']);
            unset($options['id']);
        }
        else
        {
            $this->setId();
        }
        
        if (method_exists($formElement,'dataChart'))
        {
            $dataChart = $formElement->dataChart($events);
            switch($dataChart['type'])
            {
                case self::VISUAL_TYPE_GOOGLE_CHART : 
                   return $this->_getGoogleChartHtml($options,$dataChart);
                case self::VISUAL_TYPE_WINNER_LIST:
                   return $this->_getWinnerList($options,$dataChart);
            }
        }
        return '';
        
    }
    
    protected function _getGoogleChartHtml($options,$dataChart)
    {
        $this->loadDefaultOptionsGoogleChart($options);

        $htmlTag = $this->options['htmlTag'];

        return 
        '<'.$htmlTag." id='".$this->getId()."' class='visual-rep' data-visual='"
        .  Zend_Json::encode($dataChart['data'])
        ."'></".$htmlTag.'>'."\n";
    }
    
    protected function _getWinnerList($options,array $dataChart)
    {
        $list = '';
        $title = key_exists('title',$dataChart) ? $dataChart['title'] : '';
        foreach ($dataChart['values'] as $singleData)
        {
            $href='#';
            $list .= '<li>
                <a href="'.$href.'" data-ajax="false">
                <h3>'.$singleData['value']. '</h3>'."\n"
                . '<span class="ui-li-count">'.$singleData['freqValue'].'</span>'."\n"
                . "\t". '</a>'."\n"
                . "\t".'</li>'."\n";
        }
        
        return '<div><h2>'
            . $title
            . '</h2><ul class="visual-rep" data-inset="true" id="'
            . $this->getId()
            . '" data-role="listview">'.$list
            . '</ul></div>';
    }
    
    
    public function loadDefaultOptionsGoogleChart($options)
    {
        if (!is_array($options))
        {
            $this->options = [
                'htmlTag'   => 'div'
            ];
        }
        
        
        
        return $this;
    }
    
    public function setId($id = NULL)
    {
        if ($id)
        {
            $this->_id = $id;
        }
        else
        {
            $this->_id = 'visual_'. $this->_formElement->getId();
        }
        
        return $this;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
}
