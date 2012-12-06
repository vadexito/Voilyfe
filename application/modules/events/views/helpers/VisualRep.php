<?php

class Events_View_Helper_VisualRep extends Zend_View_Helper_HtmlElement
{
  
    const VISUAL_TYPE_GOOGLE_CHART = 'google_chart';
    const VISUAL_TYPE_WINNER_LIST = 'winner_list';
    
    
    protected $_options;
    protected $_formElement;
    protected $_id = NULL;
    protected $_wrapper = NULL;
    
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
        $this->_wrapper = key_exists('wrapper',$options) ? $options['wrapper']:null;
        $this->_options = $options;
        
        if (method_exists($formElement,'dataChart'))
        {
            $dataChart = $formElement->dataChart($events);
            switch($dataChart['type'])
            {
                case self::VISUAL_TYPE_GOOGLE_CHART : 
                   return $this->_getGoogleChartHtml($dataChart);
                case self::VISUAL_TYPE_WINNER_LIST:
                   return $this->_getWinnerList($dataChart);
            }
        }
        return '';
        
    }
    
    protected function _getGoogleChartHtml($dataChart)
    {
        $this->loadDefaultOptionsGoogleChart();
        $htmlTag = $this->_options['htmlTag'];
        $attribs=[
            'id' => $this->getId(),
            'class' => 'google-chart visual-rep',
            'data-visual' => Zend_Json::encode($dataChart['data']),
        ];

        return
        ($this->_wrapper ? '<'.$this->_wrapper.'>' : '')
        . '<'.$htmlTag.$this->_htmlAttribs($attribs). "'></".$htmlTag.'>'."\n"
        . ($this->_wrapper ? '</'.$this->_wrapper.'>'."\n" : '');
    }
    
    protected function _getWinnerList(array $dataChart)
    {
        if (!$dataChart['values'])
        {
            return '';
        }
        
        $list = '';
        $htmlTag = key_exists('htmlTag', $this->_options) ? 
            $this->_options['htmlTag'] : 'div';
                
        $title = key_exists('title',$dataChart) ? $dataChart['title'] : '';
        foreach ($dataChart['values'] as $value => $freq)
        {
            if ($value)
            {
                $href='#';
                $list .= '<li>
                    <a href="'.$href.'" data-ajax="false">
                    <h3>'.$value. '</h3>'."\n"
                    . '<span class="ui-li-count">'.$freq.'</span>'."\n"
                    . "\t". '</a>'."\n"
                    . "\t".'</li>'."\n";
            }
        }
        
        return ($this->_wrapper ? '<'.$this->_wrapper.'>' : '')
            .'<'.$htmlTag.' class="visual-rep" id="'
            . $this->getId().'"><h2>'
            . $title
            . '</h2><ul class="visual-rep-list" data-inset="true" id="'
            . $this->getId().'-list'
            . '" data-role="listview">'.$list
            . '</ul></'.$htmlTag.'>'
            . ($this->_wrapper ? '</'.$this->_wrapper.'>'."\n" : '');
    }
    
    
    public function loadDefaultOptionsGoogleChart()
    {
        if (!is_array($this->_options))
        {
            $this->_options = [
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
