<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grafik
 *
 * @author Magda
 */

namespace Infogold\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="grafik")
 * @ORM\Entity()
 */
class Grafik {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id; 
    
     /**
     * @ORM\Column(type="date")
     */
    protected $data;
    

     /**
     * @ORM\Column(type="time")
     */
    protected $czasrozpoczecia;
    
    
     /**
     * @ORM\Column(type="time")
     */
    protected $czaszakonczenia;
    
     /**
     * @ORM\Column(type="integer")  
     */
    protected $minutypracy; 
    
    
    /**
     * @ORM\Column(type="text", length=180, nullable=true)
     */
    protected $komentarz;
    /**
     * @ORM\ManyToOne(targetEntity="Infogold\AccountBundle\Entity\Dzialy", inversedBy="grafik")
     * @ORM\JoinColumn(name="GrafikDzialy_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $GrafikDzialy;
    

        /**
        * @ORM\ManyToOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="KonsultantGrafik")
        * @ORM\JoinColumn(name="GrafikKonsultanta_id", referencedColumnName="id", onDelete="CASCADE")
        */
    protected $GrafikKonsultanta;

  
     public function __construct() {
      
        $this->GrafikKonsultanta = new ArrayCollection();
        
    }

  




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
     * Set data
     *
     * @param \DateTime $data
     *
     * @return Grafik
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set czasrozpoczecia
     *
     * @param \DateTime $czasrozpoczecia
     *
     * @return Grafik
     */
    public function setCzasrozpoczecia($czasrozpoczecia)
    {
        $this->czasrozpoczecia = $czasrozpoczecia;

        return $this;
    }

    /**
     * Get czasrozpoczecia
     *
     * @return \DateTime
     */
    public function getCzasrozpoczecia()
    {
        return $this->czasrozpoczecia;
    }

    /**
     * Set czaszakonczenia
     *
     * @param \DateTime $czaszakonczenia
     *
     * @return Grafik
     */
    public function setCzaszakonczenia($czaszakonczenia)
    {
        $this->czaszakonczenia = $czaszakonczenia;

        return $this;
    }

    /**
     * Get czaszakonczenia
     *
     * @return \DateTime
     */
    public function getCzaszakonczenia()
    {
        return $this->czaszakonczenia;
    }

    /**
     * Set minutypracy
     *
     * @param integer $minutypracy
     *
     * @return Grafik
     */
    public function setMinutypracy($minutypracy)
    {
        $this->minutypracy = $minutypracy;

        return $this;
    }

    /**
     * Get minutypracy
     *
     * @return integer
     */
    public function getMinutypracy()
    {
        return $this->minutypracy;
    }

    /**
     * Set komentarz
     *
     * @param string $komentarz
     *
     * @return Grafik
     */
    public function setKomentarz($komentarz)
    {
        $this->komentarz = $komentarz;

        return $this;
    }

    /**
     * Get komentarz
     *
     * @return string
     */
    public function getKomentarz()
    {
        return $this->komentarz;
    }

    /**
     * Set grafikDzialy
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $grafikDzialy
     *
     * @return Grafik
     */
    public function setGrafikDzialy(\Infogold\AccountBundle\Entity\Dzialy $grafikDzialy = null)
    {
        $this->GrafikDzialy = $grafikDzialy;

        return $this;
    }

    /**
     * Get grafikDzialy
     *
     * @return \Infogold\AccountBundle\Entity\Dzialy
     */
    public function getGrafikDzialy()
    {
        return $this->GrafikDzialy;
    }

    /**
     * Set grafikKonsultanta
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta
     *
     * @return Grafik
     */
    public function setGrafikKonsultanta(\Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta = null)
    {
        $this->GrafikKonsultanta = $grafikKonsultanta;

        return $this;
    }

    /**
     * Get grafikKonsultanta
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant
     */
    public function getGrafikKonsultanta()
    {
        return $this->GrafikKonsultanta;
    }
}
