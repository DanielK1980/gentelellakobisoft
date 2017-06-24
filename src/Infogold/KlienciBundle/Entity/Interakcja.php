<?php

namespace Infogold\KlienciBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Table(name="interakcje")
 * @ORM\Entity()
 */
class Interakcja {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    
      /**
     * @ORM\Column(type="text", length=180)
     */
    protected $opisinterakcji;

        /**
  * @ORM\ManyToOne(targetEntity="Infogold\KlienciBundle\Entity\Klienci", inversedBy="InterakcjaKlienta", cascade={"persist"})
  * @ORM\JoinColumn(name="Klient_id", referencedColumnName="id")
     */
    protected $Klient;
    
         /**
  * @ORM\ManyToOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="InterakcjaKonsultanta", cascade={"persist"})
  * @ORM\JoinColumn(name="UtworzonyPrzez_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $UtworzonyPrzez;

 /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;


   

    
      public function __toString(){
        
        return $this->getOpisInterakcji();
        
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
     * Set created
     *
     * @param \DateTime $created
     * @return Interakcja
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
     * Set Klient
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $klient
     * @return Interakcja
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
     * Set UtworzonyPrzez
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $utworzonyPrzez
     * @return Interakcja
     */
    public function setUtworzonyPrzez(\Infogold\KonsultantBundle\Entity\Konsultant $utworzonyPrzez = null)
    {
        $this->UtworzonyPrzez = $utworzonyPrzez;

        return $this;
    }

    /**
     * Get UtworzonyPrzez
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getUtworzonyPrzez()
    {
        return $this->UtworzonyPrzez;
    }


    /**
     * Set opisinterakcji
     *
     * @param string $opisinterakcji
     * @return Interakcja
     */
    public function setOpisinterakcji($opisinterakcji)
    {
        $this->opisinterakcji = $opisinterakcji;

        return $this;
    }

    /**
     * Get opisinterakcji
     *
     * @return string 
     */
    public function getOpisinterakcji()
    {
        return $this->opisinterakcji;
    }
}
