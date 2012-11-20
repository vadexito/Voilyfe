<?php

/**
 *
 * decorator for form Element
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_Form_Decorator_ErrorClass
    extends Zend_Form_Decorator_Abstract
{

    protected $_placement = null;

    public function render( $content )
    {
        $element = $this->getElement();

        if( $element->hasErrors() )
        {
            $HtmlTagHelper = $this->getElement()->getDecorator('HtmlTag');
            if ($HtmlTagHelper)
            {
                $prevClass = $HtmlTagHelper->getOption('class');
                $class = $prevClass.' '.'control-group error';
                $HtmlTagHelper->setOption('class',$class);
            }
            else
            {
                $this->getElement()->addDecorator(array('twit'=>'HtmlTag'),array('tag'=>'div','class' => 'control-group error'));
            }
            
        }

        return $content;
    }

}

