<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KlienciBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\KlienciBundle\Entity\Interakcja;

class LoadInterakcjaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $interakcja = new Interakcja();
                $interakcja->setOpisInterakcji('Klient dzwonił, żeby powiedzieć, że jest zainteresowany naszą ofertą');
        $interakcja->setKlient($this->getReference('klienci'));
        $interakcja->setUtworzonyPrzez($this->getReference('konsultant'));
       
 
        
        
        
        
        $manager->persist($interakcja);
        $manager->flush();
        
        $this->addReference('interakcja', $interakcja);
    }
    public function getOrder() {
       return 8;
    }
}