<?php

namespace Infogold\KlienciBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="faktura")
 * @ORM\Entity()
 */
class Faktura {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @ORM\Column(type="string", length=80)
     */
    
     protected $nrfaktury;
     
        /**
     * @ORM\Column(type="string", length=80)
     */
    
     protected $platnosc;
     
     /**
     * @ORM\Column(type="integer")
     */
    
     protected $rodzaj;
     
            /**
     * @ORM\Column(type="string", length=180)
     */
    
     protected $slownie;
     
                /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $datafaktury;
    
                  /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $terminplatnosci;
    
      /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\ProduktyKlienta", mappedBy="zakup", cascade={"persist"})
     */
    protected $sprzedaz;
    
     /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\ProduktyKlienta", mappedBy="proforma", cascade={"persist"})
     */
    protected $fakturaproforma;
    
      /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $wartoscnetto;
    
      /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $wartoscbrutto;
    
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Infogold\KlienciBundle\Entity\Klienci", inversedBy="faktura")
     * @ORM\JoinColumn(name="dlaklienta_id", referencedColumnName="id")
     */
    protected $dlaklienta;
    
    
   /**
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="faktury")
     * @ORM\JoinColumn(name="userfaktury_id", referencedColumnName="id")
     */
    protected $userfaktury;

    public function __construct() {

        
        $this->sprzedaz = new ArrayCollection();
        
    }
    
     public function __toString() {

        return $this->getNrfaktury();
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
     * Set nrfaktury
     *
     * @param string $nrfaktury
     * @return Faktura
     */
    public function setNrfaktury($nrfaktury)
    {
        $this->nrfaktury = $nrfaktury;

        return $this;
    }

    /**
     * Get nrfaktury
     *
     * @return string 
     */
    public function getNrfaktury()
    {
        return $this->nrfaktury;
    }

    /**
     * Set platnosc
     *
     * @param string $platnosc
     * @return Faktura
     */
    public function setPlatnosc($platnosc)
    {
        $this->platnosc = $platnosc;

        return $this;
    }

    /**
     * Get platnosc
     *
     * @return string 
     */
    public function getPlatnosc()
    {
        return $this->platnosc;
    }

    /**
     * Set rodzaj
     *
     * @param integer $rodzaj
     * @return Faktura
     */
    public function setRodzaj($rodzaj)
    {
        $this->rodzaj = $rodzaj;

        return $this;
    }

    /**
     * Get rodzaj
     *
     * @return integer 
     */
    public function getRodzaj()
    {
        return $this->rodzaj;
    }

    /**
     * Set slownie
     *
     * @param string $slownie
     * @return Faktura
     */
    public function setSlownie($slownie)
    {
        $this->slownie = $slownie;

        return $this;
    }

    /**
     * Get slownie
     *
     * @return string 
     */
    public function getSlownie()
    {
        return $this->slownie;
    }

    /**
     * Set datafaktury
     *
     * @param \DateTime $datafaktury
     * @return Faktura
     */
    public function setDatafaktury($datafaktury)
    {
        $this->datafaktury = $datafaktury;

        return $this;
    }

    /**
     * Get datafaktury
     *
     * @return \DateTime 
     */
    public function getDatafaktury()
    {
        return $this->datafaktury;
    }

    /**
     * Set terminplatnosci
     *
     * @param \DateTime $terminplatnosci
     * @return Faktura
     */
    public function setTerminplatnosci($terminplatnosci)
    {
        $this->terminplatnosci = $terminplatnosci;

        return $this;
    }

    /**
     * Get terminplatnosci
     *
     * @return \DateTime 
     */
    public function getTerminplatnosci()
    {
        return $this->terminplatnosci;
    }

    /**
     * Set wartoscnetto
     *
     * @param string $wartoscnetto
     * @return Faktura
     */
    public function setWartoscnetto($wartoscnetto)
    {
        $this->wartoscnetto = $wartoscnetto;

        return $this;
    }

    /**
     * Get wartoscnetto
     *
     * @return string 
     */
    public function getWartoscnetto()
    {
        return $this->wartoscnetto;
    }

    /**
     * Set wartoscbrutto
     *
     * @param string $wartoscbrutto
     * @return Faktura
     */
    public function setWartoscbrutto($wartoscbrutto)
    {
        $this->wartoscbrutto = $wartoscbrutto;

        return $this;
    }

    /**
     * Get wartoscbrutto
     *
     * @return string 
     */
    public function getWartoscbrutto()
    {
        return $this->wartoscbrutto;
    }

    /**
     * Add sprzedaz
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $sprzedaz
     * @return Faktura
     */
    public function addSprzedaz(\Infogold\KlienciBundle\Entity\ProduktyKlienta $sprzedaz)
    {
        $this->sprzedaz[] = $sprzedaz;

        return $this;
    }

    /**
     * Remove sprzedaz
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $sprzedaz
     */
    public function removeSprzedaz(\Infogold\KlienciBundle\Entity\ProduktyKlienta $sprzedaz)
    {
        $this->sprzedaz->removeElement($sprzedaz);
    }

    /**
     * Get sprzedaz
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSprzedaz()
    {
        return $this->sprzedaz;
    }

    /**
     * Add fakturaproforma
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $fakturaproforma
     * @return Faktura
     */
    public function addFakturaproforma(\Infogold\KlienciBundle\Entity\ProduktyKlienta $fakturaproforma)
    {
        $this->fakturaproforma[] = $fakturaproforma;

        return $this;
    }

    /**
     * Remove fakturaproforma
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $fakturaproforma
     */
    public function removeFakturaproforma(\Infogold\KlienciBundle\Entity\ProduktyKlienta $fakturaproforma)
    {
        $this->fakturaproforma->removeElement($fakturaproforma);
    }

    /**
     * Get fakturaproforma
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFakturaproforma()
    {
        return $this->fakturaproforma;
    }

    /**
     * Set dlaklienta
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $dlaklienta
     * @return Faktura
     */
    public function setDlaklienta(\Infogold\KlienciBundle\Entity\Klienci $dlaklienta = null)
    {
        $this->dlaklienta = $dlaklienta;

        return $this;
    }

    /**
     * Get dlaklienta
     *
     * @return \Infogold\KlienciBundle\Entity\Klienci 
     */
    public function getDlaklienta()
    {
        return $this->dlaklienta;
    }

    /**
     * Set userfaktury
     *
     * @param \Infogold\UserBundle\Entity\User $userfaktury
     * @return Faktura
     */
    public function setUserfaktury(\Infogold\UserBundle\Entity\User $userfaktury = null)
    {
        $this->userfaktury = $userfaktury;

        return $this;
    }

    /**
     * Get userfaktury
     *
     * @return \Infogold\UserBundle\Entity\User 
     */
    public function getUserfaktury()
    {
        return $this->userfaktury;
    }
}
