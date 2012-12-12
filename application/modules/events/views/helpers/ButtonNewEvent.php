<?php

class Events_View_Helper_ButtonNewEvent extends Zend_View_Helper_Abstract
{
    
    public function buttonNewEvent()
    {
        $view = $this->view;
        $wrap = ['tag' => 'div','class' => 'row-fluid'];
        
        // add button for right page
        
        if (!property_exists($view,'form') && (!$view->allOption) &&
            (property_exists($view,'category') && is_object($view->category->categories) && $view->category->categories->count()=== 0))
        {
            
            return '<'.$wrap['tag'].' class="'.$wrap['class'].'">
                <a id="new-event-btn" href ="'
            .$view->url(
                ['action' => 'create','containerId'=> $view->category->id],
                'event'
            )
            .'" class="pull-right btn btn-primary btn-large">'."\n"
            .'<i class="icon-edit icon-white"></i> '
            .$view->translate('menu_createNewEvent')
            .'</a>
                </div>';
        }
        return '';
    }
}
