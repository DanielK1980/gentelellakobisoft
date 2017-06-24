<?php

namespace Infogold\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="raport")
 * @ORM\Entity()
 */
class RaportyPrzerw {
    
        /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
 
     /**
  * @ORM\ManytoOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="raportprzerw",  cascade={"persist"})
  * @ORM\JoinColumn(name="KonsultantRaportPrzerw_id", referencedColumnName="id",  nullable=true,  onDelete="CASCADE")))
     */
    protected $KonsultantRaportPrzerw;
    
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
     * @ORM\Column(type="time")  
     */
    protected $czasprzerwy; 
 
   
    
    

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
     * @return RaportyPrzerw
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
     * @return RaportyPrzerw
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
     * @return RaportyPrzerw
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
     * Set czasprzerwy
     *
     * @param integer $czasprzerwy
     * @return RaportyPrzerw
     */
    public function setCzasprzerwy($czasprzerwy)
    {
        $this->czasprzerwy = $czasprzerwy;

        return $this;
    }

    /**
     * Get czasprzerwy
     *
     * @return time 
     */
    public function getCzasprzerwy()
    {
        return $this->czasprzerwy;
    }

    /**
     * Set KonsultantRaportPrzerw
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $konsultantRaportPrzerw
     * @return RaportyPrzerw
     */
    public function setKonsultantRaportPrzerw(\Infogold\KonsultantBundle\Entity\Konsultant $konsultantRaportPrzerw = null)
    {
        $this->KonsultantRaportPrzerw = $konsultantRaportPrzerw;

        return $this;
    }

    /**
     * Get KonsultantRaportPrzerw
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getKonsultantRaportPrzerw()
    {
        return $this->KonsultantRaportPrzerw;
    }
}
