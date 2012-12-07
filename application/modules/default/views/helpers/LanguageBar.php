<?php

class Application_View_Helper_LanguageBar extends Zend_View_Helper_Abstract
{
    use Pepit_Locale_Trait;
    
    protected $_indent;
    
    public function languageBar($indent = NULL)
    {
        $this->setIndent($indent);
        
        $config = new Zend_Config_Xml(
            APPLICATION_PATH.'/modules/backend/Data/Init/InitialData.xml',
            'init'
        );
        $locales = $config->language->type;
        $links = '';
        
        $currentLocale = $this->findLocale();
        
        foreach ($locales as $locale)
        {
            $href = $this->view->url(
                array('localeName' => $locale->value),
                'set-locale'
                );
            $link= $this->_indent.'<li><a tabindex="-1" title="'
                .Zend_Locale::getTranslation($locale->value,'language',$currentLocale)
                .'" href="'. $href .'">'
                .Zend_Locale::getTranslation($locale->value,'language',$locale->value)
                .'</a></li>'."\n";
                
            if ($locale->value == 'en')
            {
                $linkEnglish = $link;
            }
            else
            {
                if ($locale->value != $currentLocale)
                {
                    $links.=$link;
                }
            }
        }
        $currentLocaleTranslated = zend_locale::getTranslation($currentLocale,'language',$currentLocale);
        $title = '<small>'.ucfirst($this->view->translate("menu_language"))
                .'</small>'."\n".'<span class="current-lang">'.$currentLocaleTranslated.'</span>';
        
        return $this->view->navigBar($title,'#',$links,$linkEnglish);
    }
    
    public function setIndent($nb=NULL)
    {
        if ($nb === NULL)
        {
            $this->_indent = "\t\t\t\t\t";
        }
    }
        
} 
