<?php


namespace Infogold\KlienciBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\KlienciBundle\Entity\RodzajeKontaktow;

class LoadRodzajeKontaktowData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $Rodzajekontaktow = new RodzajeKontaktow();
        $Rodzajekontaktow->setName('Spotkanie');
        
        
        $Rodzajekontaktow1 = new RodzajeKontaktow();
        $Rodzajekontaktow1->setName('Telefon');
        
        $Rodzajekontaktow2 = new RodzajeKontaktow();
        $Rodzajekontaktow2->setName('Email');
        
        $manager->persist($Rodzajekontaktow);
        $manager->persist($Rodzajekontaktow1);
        $manager->persist($Rodzajekontaktow2);
        $manager->flush();
        
        $this->addReference('rodzajekontaktow', $Rodzajekontaktow);
        $this->addReference('rodzajekontaktow1', $Rodzajekontaktow1);
        $this->addReference('rodzajekontaktow2', $Rodzajekontaktow2);
    }
    public function getOrder() {
        return 6;
    }
}