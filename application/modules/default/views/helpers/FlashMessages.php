<?php

class Application_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
    public function flashMessages()
    {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        if (empty($messages))
        {
            return '';
        }
        $output = '<ul id="messages">';
        foreach ($messages as $message) 
        {
            $output .= '<p class="box info">' . $message . '</p>';
        }
        
        return $this->render($output.'</ul>');
    }
    
    public function render($content)
    {
        return '<div id="flash" class="alert alert-block alert-success">'
            ."\n"
            .'<button type="button" class="close" data-dismiss="alert">×</button>'
            .$content.'</div>';
    }
}
