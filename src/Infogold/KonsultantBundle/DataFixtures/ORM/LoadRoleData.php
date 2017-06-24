<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KonsultantBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\KonsultantBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $Role1 = new Role();
        $Role1->setName('Konsultant');
        $Role1->setRoles('ROLE_KONSULTANT');
        
        $Role2 = new Role();
        $Role2->setName('Starszy Konsultant');
        $Role2->setRoles('ROLE_STARSZY');
        
        $Role3 = new Role();
        $Role3->setName('Specjalista');
        $Role3->setRoles('ROLE_SPECJALISTA');        
        
        $Role4 = new Role();
        $Role4->setName('Lider');
        $Role4->setRoles('ROLE_LIDER');
        
        $manager->persist($Role1);
        $manager->persist($Role2);
        $manager->persist($Role3);
        $manager->persist($Role4);
        $manager->flush();
        
        $this->addReference('role1', $Role1);
        $this->addReference('role2', $Role2);
        $this->addReference('role3', $Role3);
        $this->addReference('role4', $Role4);
        
    }
    public function getOrder() {
        return 1;
    }
}
