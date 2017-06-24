<?php

namespace Infogold\KlienciBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Table(name="kontakty")
 * @ORM\Entity()
 */
class Kontakty {
    
  

        /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /**
  * @ORM\ManyToOne(targetEntity="Klienci", inversedBy="KontaktKlienta", cascade={"persist"})
  * @ORM\JoinColumn(name="Klient_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $Klient;

   
    /**
     * @ORM\Column(type="text", length=180)
     */
    protected $opiskontaktu;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $danekontaktowe;

  /**
  * @ORM\ManyToOne(targetEntity="Infogold\KlienciBundle\Entity\RodzajeKontaktow", inversedBy="NazwaKontaktu", cascade={"persist"})
  * @ORM\JoinColumn(name="RodzajKontaktu_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $RodzajKontaktu;

    /**
     * @ORM\Column(type="datetime", length=10)
     */
    protected $czasKontaktu;
 
/**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;
    
    
     /**
  * @ORM\ManyToOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="KontaktKonsultanta",  cascade={"persist"})
  * @ORM\JoinColumn(name="PrzypisanyDo_id", referencedColumnName="id",  nullable=true))
     */
    protected $PrzypisanyDo;
    
    
     /**
     * @ORM\Column(type="string", length=80)
     */
    protected $utworzonyprzez;
    /**
     * Constructor
     */
    
       /**
     * @ORM\Column(type="boolean")
     */
    protected $status;
    
    
       /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $powodzamkniecia;
    
           /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $kontaktzamknietyprzez;
    
             /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $datazamkniecia;
    /**
     * Constructor
     */
    public function __construct()
    {        
               
        $this->Klient = new ArrayCollection();
        
    }

    public function __toString() {
        $this->DaneKontaktowe;
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
     * Set opiskontaktu
     *
     * @param string $opiskontaktu
     * @return Kontakty
     */
    public function setOpiskontaktu($opiskontaktu)
    {
        $this->opiskontaktu = $opiskontaktu;

        return $this;
    }

    /**
     * Get opiskontaktu
     *
     * @return string 
     */
    public function getOpiskontaktu()
    {
        return $this->opiskontaktu;
    }

    /**
     * Set danekontaktowe
     *
     * @param string $danekontaktowe
     * @return Kontakty
     */
    public function setDanekontaktowe($danekontaktowe)
    {
        $this->danekontaktowe = $danekontaktowe;

        return $this;
    }

    /**
     * Get danekontaktowe
     *
     * @return string 
     */
    public function getDanekontaktowe()
    {
        return $this->danekontaktowe;
    }

    /**
     * Set czasKontaktu
     *
     * @param \DateTime $czasKontaktu
     * @return Kontakty
     */
    public function setCzasKontaktu($czasKontaktu)
    {
        $this->czasKontaktu = $czasKontaktu;

        return $this;
    }

    /**
     * Get czasKontaktu
     *
     * @return \DateTime 
     */
    public function getCzasKontaktu()
    {
        return $this->czasKontaktu;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Kontakty
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Kontakty
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set utworzonyprzez
     *
     * @param string $utworzonyprzez
     * @return Kontakty
     */
    public function setUtworzonyprzez($utworzonyprzez)
    {
        $this->utworzonyprzez = $utworzonyprzez;

        return $this;
    }

    /**
     * Get utworzonyprzez
     *
     * @return string 
     */
    public function getUtworzonyprzez()
    {
        return $this->utworzonyprzez;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Kontakty
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set powodzamkniecia
     *
     * @param string $powodzamkniecia
     * @return Kontakty
     */
    public function setPowodzamkniecia($powodzamkniecia)
    {
        $this->powodzamkniecia = $powodzamkniecia;

        return $this;
    }

    /**
     * Get powodzamkniecia
     *
     * @return string 
     */
    public function getPowodzamkniecia()
    {
        return $this->powodzamkniecia;
    }

    /**
     * Set kontaktzamknietyprzez
     *
     * @param string $kontaktzamknietyprzez
     * @return Kontakty
     */
    public function setKontaktzamknietyprzez($kontaktzamknietyprzez)
    {
        $this->kontaktzamknietyprzez = $kontaktzamknietyprzez;

        return $this;
    }

    /**
     * Get kontaktzamknietyprzez
     *
     * @return string 
     */
    public function getKontaktzamknietyprzez()
    {
        return $this->kontaktzamknietyprzez;
    }

    /**
     * Set datazamkniecia
     *
     * @param \DateTime $datazamkniecia
     * @return Kontakty
     */
    public function setDatazamkniecia($datazamkniecia)
    {
        $this->datazamkniecia = $datazamkniecia;

        return $this;
    }

    /**
     * Get datazamkniecia
     *
     * @return \DateTime 
     */
    public function getDatazamkniecia()
    {
        return $this->datazamkniecia;
    }

    /**
     * Set Klient
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $klient
     * @return Kontakty
     */
    public function setKlient(\Infogold\KlienciBundle\Entity\Klienci $klient = null)
    {
        $this->Klient = $klient;

        return $this;
    }

    /**
     * Get Klient
     *
     * @return \Infogold\KlienciBundle\Entity\Klienci 
     */
    public function getKlient()
    {
        return $this->Klient;
    }

    /**
     * Set RodzajKontaktu
     *
     * @param \Infogold\KlienciBundle\Entity\RodzajeKontaktow $rodzajKontaktu
     * @return Kontakty
     */
    public function setRodzajKontaktu(\Infogold\KlienciBundle\Entity\RodzajeKontaktow $rodzajKontaktu = null)
    {
        $this->RodzajKontaktu = $rodzajKontaktu;

        return $this;
    }

    /**
     * Get RodzajKontaktu
     *
     * @return \Infogold\KlienciBundle\Entity\RodzajeKontaktow 
     */
    public function getRodzajKontaktu()
    {
        return $this->RodzajKontaktu;
    }

    /**
     * Set PrzypisanyDo
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $przypisanyDo
     * @return Kontakty
     */
    public function setPrzypisanyDo(\Infogold\KonsultantBundle\Entity\Konsultant $przypisanyDo = null)
    {
        $this->PrzypisanyDo = $przypisanyDo;

        return $this;
    }

    /**
     * Get PrzypisanyDo
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getPrzypisanyDo()
    {
        return $this->PrzypisanyDo;
    }
}
