<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KlienciBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\KlienciBundle\Entity\Kontakty;

class LoadKontaktyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $kontakt = new Kontakty();
        $kontakt->setUtworzonyPrzez($this->getReference('konsultant'));
        $kontakt->setKlient($this->getReference('klienci'));
        $kontakt->setRodzajKontaktu($this->getReference('rodzajekontaktow'));
        $kontakt->setOpisKontaktu('Klient będzie chciał porozmawiać w sprawie pożyczki ekspresowej');
        $kontakt->setCzasKontaktu(new \DateTime('2000-01-01'));
        
        $kontakt->setDaneKontaktowe('ul. Towarowa 5 38-200 Warszawa');
        
        
        
        
        
        
        $manager->persist($kontakt);
        $manager->flush();
        
        $this->addReference('kontakt', $kontakt);
    }
    public function getOrder() {
       return 9;
    }
}