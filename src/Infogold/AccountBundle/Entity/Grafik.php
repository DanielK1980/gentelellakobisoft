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
     * @ORM\ManyToMany(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant")
     * @ORM\JoinTable(name="grafiki_konsultantow",
     *      joinColumns={@ORM\JoinColumn(name="grafik_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="konsultant_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     **/
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
     * Set GrafikDzialy
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $grafikDzialy
     * @return Grafik
     */
    public function setGrafikDzialy(\Infogold\AccountBundle\Entity\Dzialy $grafikDzialy = null)
    {
        $this->GrafikDzialy = $grafikDzialy;

        return $this;
    }

    /**
     * Get GrafikDzialy
     *
     * @return \Infogold\AccountBundle\Entity\Dzialy 
     */
    public function getGrafikDzialy()
    {
        return $this->GrafikDzialy;
    }

    /**
     * Add GrafikKonsultanta
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta
     * @return Grafik
     */
    public function addGrafikKonsultanta(\Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta)
    {
        $this->GrafikKonsultanta[] = $grafikKonsultanta;

        return $this;
    }

    /**
     * Remove GrafikKonsultanta
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta
     */
    public function removeGrafikKonsultanta(\Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta)
    {
        $this->GrafikKonsultanta->removeElement($grafikKonsultanta);
    }

    /**
     * Get GrafikKonsultanta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGrafikKonsultanta()
    {
        return $this->GrafikKonsultanta;
    }
    
    public function getMinutypracy() {
        return $this->minutypracy;
    }

    public function setMinutypracy($minutypracy) {
        $this->minutypracy = $minutypracy;
    }



    /**
     * Add GrafikKonsultanta
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta
     * @return Grafik
     */
    public function addGrafikKonsultantum(\Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta)
    {
        $this->GrafikKonsultanta[] = $grafikKonsultanta;

        return $this;
    }

    /**
     * Remove GrafikKonsultanta
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta
     */
    public function removeGrafikKonsultantum(\Infogold\KonsultantBundle\Entity\Konsultant $grafikKonsultanta)
    {
        $this->GrafikKonsultanta->removeElement($grafikKonsultanta);
    }
    function getKomentarz() {
        return $this->komentarz;
    }

    function setKomentarz($komentarz) {
        $this->komentarz = $komentarz;
    }


}
