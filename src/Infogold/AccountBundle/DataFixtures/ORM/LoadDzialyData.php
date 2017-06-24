<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KonsultantBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\AccountBundle\Entity\Dzialy;

class LoadDzialyData extends AbstractFixture implements OrderedFixtureInterface
{
       /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $dzial = new Dzialy();
        $dzial->setName('Dzial sprzedaży');
        $dzial->setDziałyFirmy($this->getReference('firma'));
        $dzial->setLimityprzerw('5');
        
         $dzial2 = new Dzialy();
        $dzial2->setName('Dzial księgowości');
        $dzial2->setDziałyFirmy($this->getReference('firma'));
        $dzial2->setLimityprzerw('3');
                
        $manager->persist($dzial);
        $manager->persist($dzial2);
        $manager->flush();
        
        $this->addReference('dzial', $dzial);
         $this->addReference('dzial2', $dzial2);
    }
    public function getOrder() {
       return 3;
    }
}
    
    
