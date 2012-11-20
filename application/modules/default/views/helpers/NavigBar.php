<?php

class Application_View_Helper_NavigBar extends Zend_View_Helper_Abstract
{
    public function navigBar($title,$hrefTitle = "#",
            $links = '',$separatedLinks = '')
    {
        $caret = '';
        $optionToogle = '';
        
        if (!($separatedLinks === ''))
        {
            $separatedLinks = '<li class="divider"></li>'
                            .$separatedLinks;
        }
        if (!($links === ''))
        {
            $links='<ul class="dropdown-menu">'."\n"
                            .$links.
                            '<li class="divider"></li>'
                            .$separatedLinks.
                        '</ul>';
            $caret = '<b class="caret"></b>';
            $optionToogle = ' data-toggle="dropdown" ';
        }
        return '
        <div id="nav-languages" class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <ul class="nav pull-right">
                        <li class="dropdown">                  
                            <a class="dropdown-toggle" data-toggle="dropdown" role="button"'.$optionToogle.'href="'.$hrefTitle.'">'."\n\t\t\t\t".
                                $title.$caret.'
                            </a>
                                '.$links.'
                        </li>
                    </ul>
                </div>
            </div>
       </div>'."\n";
    }


        
} 
