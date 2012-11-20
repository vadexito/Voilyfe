<?php

/**
 *
 * Pepit_AssociationType
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_AssociationType_Factory
{
    static public function build(
        $associationType, Doctrine\ORM\EntityManager $em)
    {
        switch($associationType)
        {
            case Pepit_AssociationType::MAP_FIELD :
                return new Pepit_AssociationType_MapField($em);
                break;
            case Pepit_AssociationType::ASSOC_ONE_TO_MANY :
                return new Pepit_AssociationType_OneToMany($em);
                break;
            case Pepit_AssociationType::ASSOC_ONE_TO_ONE :
                return new Pepit_AssociationType_OneToOne($em);
                break;
            case Pepit_AssociationType::ASSOC_MANY_TO_MANY :
                return new Pepit_AssociationType_ManyToMany($em);
                break;
            case Pepit_AssociationType::ASSOC_MANY_TO_ONE :
                return new Pepit_AssociationType_ManyToOne($em);
                break;
        }
    }
}

