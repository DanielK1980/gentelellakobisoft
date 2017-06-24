<?php
// src/UserBundle/Entity/Konsultant.php

namespace Infogold\KonsultantBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Infogold\KonsultantBundle\Entity\Kolejki
 * @ORM\Entity
 * @ORM\Table(name="kolejki")
 */
class Kolejki  {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

       /**
     * @ORM\ManytoOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="kolejka")
     * @ORM\JoinColumn(name="KonsultantKolejka_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    protected $KonsultantKolejka;
   
        /**
     * @ORM\ManytoOne(targetEntity="Infogold\AccountBundle\Entity\Dzialy", inversedBy="DzialyKolejki")
     * @ORM\JoinColumn(name="KolejkaDzialu_id", referencedColumnName="id",  onDelete="CASCADE"))
     */
    protected $KolejkaDzialu;


/**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
  

    /**
     * Set KonsultantKolejka
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $konsultantKolejka
     * @return Kolejki
     */
    public function setKonsultantKolejka(\Infogold\KonsultantBundle\Entity\Konsultant $konsultantKolejka = null)
    {
        $this->KonsultantKolejka = $konsultantKolejka;

        return $this;
    }

    /**
     * Get KonsultantKolejka
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getKonsultantKolejka()
    {
        return $this->KonsultantKolejka;
    }

    /**
     * Set KolejkaDzialu
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $kolejkaDzialu
     * @return Kolejki
     */
    public function setKolejkaDzialu(\Infogold\AccountBundle\Entity\Dzialy $kolejkaDzialu = null)
    {
        $this->KolejkaDzialu = $kolejkaDzialu;

        return $this;
    }

    /**
     * Get KolejkaDzialu
     *
     * @return \Infogold\AccountBundle\Entity\Dzialy 
     */
    public function getKolejkaDzialu()
    {
        return $this->KolejkaDzialu;
    }
    
     /**
     * Set created
     *
     * @param \DateTime $created
     * @return Kolejka
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }
    
       public function getCreated() {
        return $this->created;
    }

}
