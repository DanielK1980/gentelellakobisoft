<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Infogold\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    
    
     /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        $userManager = $this->container->get('fos_user.user_manager');
        $userUser = $userManager->createUser();
        
        $userUser->setNrklienta(12345678);
        $userUser->setImie('Jan');
        $userUser->setNazwisko('Kowalski');
        $userUser->setTelefon('12345678');
        $userUser->setUsername('Janek');
        $userUser->setDomena('BR');
        $userUser->setPassword('pass');
        $userUser->setPlainPassword('pass');     
        $userUser->setEmail('jan.kowalski@gmail.com');
        $userUser->setKodpocztowy('38-300');
        $userUser->setMiejscowosc('Warszawa');
        $userUser->setNazwafirmy('Grupa Infogold');
        $userUser->setEnabled(true);
        $userUser->setUlica('Krakowska 25');
        $userUser->setNip('888888888');
        $userUser->setRoles(array('ROLE_USER'));
       
        
        
        
        
        
         $userManager->updateUser($userUser, true);
        $manager->persist($userUser);
        $manager->flush();
        
        
        
        $this->addReference('firma', $userUser);
    }
    
     public function getOrder() {
       return 2;
    }
}
