<?php

class Application_View_Helper_Widget extends Zend_View_Helper_Abstract
{
    public function widget(\ZC\Entity\Category $category,$modelCategories,
        \ZC\Entity\Member $member)
    {
        $totalOverYear = $modelCategories->getTotal($member->id,'year');
        $topPerformance = $modelCategories->stat('top','year');
        $performance = $totalOverYear / $topPerformance;
        
        $content = '<h3>'.ucfirst($category->name).'</h3>';
        $content.= '<p>'.$totalOverYear.' over the year</p>';
        $content.= '<a href="#">More ?</a>';
        $content.= '<p> Performing :'.$performance.'% compared to the best. </p>';
        
        return $content;
  
    }
}
