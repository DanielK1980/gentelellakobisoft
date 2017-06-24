<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KonsultantBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\KonsultantBundle\Entity\Konsultant;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadKonsultantData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface 
{
    
    
    public $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container ;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userAdmin = new Konsultant();
        $userAdmin->setUsername('admin');
        $userAdmin->setImie('Administratorek');
        $userAdmin->setNazwisko('Administraczyk');
        
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($userAdmin);
        $password = $encoder->encodePassword('test', $userAdmin->getSalt());
        $userAdmin->setPassword($password);
                
        $userAdmin->setUpdatePassword(new \DateTime('now'));
        $userAdmin->setOldpassword($password);
        $userAdmin->setEmail('Ja@gmail.com');
        $userAdmin->setIsActive(true);
        $userAdmin->setKonsultantRoles($this->getReference('role1'));
        $userAdmin->setFirma($this->getReference('firma'));   
        $userAdmin->setKonsultantDzialy($this->getReference('dzial'));
        
        
        
        $manager->persist($userAdmin);
        $manager->flush();
        
        $this->addReference('konsultant', $userAdmin);
        
        
        
             $userAdmin2 = new Konsultant();
        $userAdmin2->setUsername('admin2');
        $userAdmin2->setImie('Administratorka');
        $userAdmin2->setNazwisko('Administraczyk');
        $factory2 = $this->container->get('security.encoder_factory');
        $encoder2 = $factory2->getEncoder($userAdmin2);
        $password2 = $encoder2->encodePassword('test2', $userAdmin2->getSalt());
        $userAdmin2->setPassword($password2);
                
        $userAdmin2->setUpdatePassword(new \DateTime('now'));
        $userAdmin2->setOldpassword($password2);
        $userAdmin2->setEmail('Ja2@gmail.com');
        $userAdmin2->setIsActive(true);
        $userAdmin2->setKonsultantRoles($this->getReference('role1'));
        $userAdmin2->setFirma($this->getReference('firma'));
        $userAdmin2->setKonsultantDzialy($this->getReference('dzial'));
        
        
        
        
        $manager->persist($userAdmin2);
        $manager->flush();
        
        $this->addReference('konsultant2', $userAdmin2);
    }
    public function getOrder() {
       return 4;
    }
}
