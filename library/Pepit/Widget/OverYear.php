<?php

/*
* @author DM
* 
*/
class Pepit_Widget_OverYear extends Zend_Form_Element
{
    
    protected $totalOverYearMember;
    
    protected $totalOverYearBest;
    
    
    public function render(Zend_View_Interface $view = NULL)
    {
        $totalOverYear = $this->totalOverYearMember;
        $topPerformance = $this->totalOverYearBest;
        if (!($topPerformance >0))
        {
            return $this->getTranslator()->translate('msg_no_performance_available');
        }
        $performance = $totalOverYear / $topPerformance;
        $content = '<h4><img src="/images/images.jpg" class="img-circle">  '.ucfirst($this->getLabel())
                    . '<a href="#" class="btn pull-right btn-small">'
                    .$this->getTranslator()->translate('button_see_more').'</a>'."\n".'</h4>'."\n"
                    . 'bydhfdkfjg dfgd '."\n"
                    . '<li class="widget-divider"></li>'."\n"
                    .'<p><i class="icon-search"></i> '.$totalOverYear.' over the year</p>'."\n"
                    .'<p><i class="icon-search"></i> '.$totalOverYear.' over the year</p>'."\n"
                    .'<p><i class="icon-search"></i>  Performing: '.round($performance*100,0).'% compared to the best. </p>'."\n";
        
        return $content;
    }
    
    public function setTotalOverYearMember($total)
    {
        $this->totalOverYearMember = $total;
    }
    
    public function setTotalOverYearBest($total)
    {
        $this->totalOverYearBest = $total;
    }
    

}
