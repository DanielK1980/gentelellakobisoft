<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Przerwy
 *
 * @author Magda
 */

namespace Infogold\KonsultantBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Infogold\KonsultantBundle\Entity\Przerwy
 * @ORM\Entity
 * @ORM\Table(name="przerwy")
 */
class Przerwy {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

       /**
     * @ORM\ManytoOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="przerwa")
     * @ORM\JoinColumn(name="KonsultantPrzerwa_id", referencedColumnName="id",  onDelete="CASCADE"))
     */
    protected $KonsultantPrzerwa;
   
        /**
     * @ORM\ManytoOne(targetEntity="Infogold\AccountBundle\Entity\Dzialy", inversedBy="DzialyPrzerwy")
     * @ORM\JoinColumn(name="PrzerwaDzialu_id", referencedColumnName="id",  onDelete="CASCADE"))
     */
    protected $PrzerwaDzialu;  

       /**
     * @ORM\Column(type="datetime")
     */
    protected $czasrozpoczecia;  


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
     * Set KonsultantPrzerwa
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $konsultantPrzerwa
     * @return Przerwy
     */
    public function setKonsultantPrzerwa(\Infogold\KonsultantBundle\Entity\Konsultant $konsultantPrzerwa = null)
    {
        $this->KonsultantPrzerwa = $konsultantPrzerwa;

        return $this;
    }

    /**
     * Get KonsultantPrzerwa
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getKonsultantPrzerwa()
    {
        return $this->KonsultantPrzerwa;
    }

    /**
     * Set PrzerwaDzialu
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $przerwaDzialu
     * @return Przerwy
     */
    public function setPrzerwaDzialu(\Infogold\AccountBundle\Entity\Dzialy $przerwaDzialu = null)
    {
        $this->PrzerwaDzialu = $przerwaDzialu;

        return $this;
    }

    /**
     * Get PrzerwaDzialu
     *
     * @return \Infogold\AccountBundle\Entity\Dzialy 
     */
    public function getPrzerwaDzialu()
    {
        return $this->PrzerwaDzialu;
    }
    
    function getCzasrozpoczecia() {
        return $this->czasrozpoczecia;
    }

    function setCzasrozpoczecia($czasrozpoczecia) {
        $this->czasrozpoczecia = $czasrozpoczecia;
    }


}
