<?php

class Events_View_Helper_ButtonNewEvent extends Zend_View_Helper_Abstract
{
    
    public function buttonNewEvent($categoryId)
    {
        if ($categoryId)
        {
            return '<a id="new-event-btn" href ="'
            .$this->view->url(
                    array(
                        'action' => 'create',
                        'containerId'=> $categoryId),
                    'event'
            )
            .'" class="pull-right btn btn-primary btn-large">'."\n"
            .'<i class="icon-edit icon-white"></i> '.$this->view->translate('menu_createNewEvent')
            .'</a>';
        }
        return '';
    }
}
