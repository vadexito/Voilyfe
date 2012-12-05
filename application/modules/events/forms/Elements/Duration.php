<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Duration extends Pepit_Form_Element_Range
{

    public function init()
    {
        $this->setOptions([
                "required" => false,
                "validators" => ['Float'],
                "value" => 0
            ])
             ->setLabel('item_duration');
        parent::init();
        
        $this->setAttribs([
            'min' => '0',
            'max' => '120',
            'step' => '5',
        ]);
    }
    
    public function dataChart($events)
    {
        $options = [
            'title' => ucfirst($this->getTranslator()->translate('title_total_time')),
            'type' => 'sum',
            'propertyForAdding' => $this->getName(),
            'unit' => 'minutes',
            'timeUnitNb' => 1,
            'timeUnit' => 'month',
            'hAxisTitle' => ucfirst($this->getTranslator()->translate('unit_month')),
        ];
        
        return [
            'type' => 'google_chart',
            'data' => (new Pepit_Widget_Chart($events,$options))->dataForGoogleCharts()
        ];
    }

}

