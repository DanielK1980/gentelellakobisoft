<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KonsultantBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\AccountBundle\Entity\Produkty;

class LoadProduktyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $produkty = new Produkty();
        $produkty->setNrproduktu('11');
        $produkty->setOpis('Pożyczka Expresowa');
        $produkty->setCenaProduktu('1000 zł');
        $produkty->setName('PEX');       
        $produkty->setUserproduktu($this->getReference('firma'));
        $produkty->setProduktKlienta($this->getReference('klienci'));             
        $manager->persist($produkty);
        $manager->flush();
        
        $this->addReference('produkty', $produkty);
    }
    public function getOrder() {
       return 7;
    }
}