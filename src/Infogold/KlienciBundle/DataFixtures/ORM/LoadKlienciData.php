<?php

// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace Infogold\KlienciBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Infogold\KlienciBundle\Entity\Klienci;

class LoadKliencitData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $klienci = new Klienci();
        
        $klienci->setNrDomu('7');
        $klienci->setNrMieszkania('22');
        $klienci->setNumerKlienta('12345678');
        $klienci->setNazwaKlienta('Grupa Infogold');
        $klienci->setEmailKlienta('skrzynkadaniela@gmail.com');  
        $klienci->setImie('Daniel');
        $klienci->setNazwisko('Kaszyk');
        $klienci->setNipKlienta('789654321');
        $klienci->setPeselKlienta(''); 
        $klienci->setStronaWWWKlienta('www.infogold.pl');
        $klienci->setTelefonKlienta('729123925');  
        $klienci->setMiasto('Gorlice');
        $klienci->setKodPocztowy('38-300');
        $klienci->setUlica('Krakowska');
        $klienci->setWojewodztwo('Małopolskie');       
        
        $klienci->setKlientKonsultanta($this->getReference('konsultant'));
        $klienci->setUser($this->getReference('firma'));
        
        
        
        $klienci2 = new Klienci();
        
        $klienci2->setNrDomu('7');
        $klienci2->setNrMieszkania('22');
        $klienci2->setNumerKlienta('12345689');
        $klienci2->setNazwaKlienta('Grupa Infogold');
        $klienci2->setEmailKlienta('skrzynkadaniela@gmail.com');  
        $klienci2->setImie('Daniel');
        $klienci2->setNazwisko('Kaszyk');
        $klienci2->setNipKlienta('');
        $klienci2->setPeselKlienta('80091017697'); 
        $klienci2->setStronaWWWKlienta('www.infogold.pl');
        $klienci2->setTelefonKlienta('729123925');  
        $klienci2->setMiasto('Gorlice');
        $klienci2->setKodPocztowy('38-300');
        $klienci2->setUlica('Krakowska');
        $klienci2->setWojewodztwo('Małopolskie');       
       
        $klienci2->setKlientKonsultanta($this->getReference('konsultant'));
         $klienci2->setUser($this->getReference('firma'));
        
       
     
        
        $manager->persist($klienci);
        $manager->persist($klienci2);
        $manager->flush();
        
        $this->addReference('klienci', $klienci);
    }
    public function getOrder() {
       return 5;
    }
}