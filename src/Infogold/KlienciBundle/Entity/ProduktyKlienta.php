<?php

namespace Infogold\KlienciBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="produkty")
 * @ORM\Entity()
 */
class ProduktyKlienta {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $ilosc;
    
          /**
     * @ORM\Column(type="string", length=80)
     */
    protected $jednostkamiary;

    /**
     * @ORM\ManyToMany(targetEntity="Infogold\KlienciBundle\Entity\Klienci", inversedBy="KlientaProdukty")
     * @ORM\JoinTable(name="produkty_klientow")
     * */
    protected $ProduktyKlienta;

    /**
     * @ORM\ManyToOne(targetEntity="Infogold\AccountBundle\Entity\Produkt", inversedBy="produkt")
     * @ORM\JoinColumn(name="produkty_id", referencedColumnName="id",nullable=true)
     */
    protected $produkty;

           /**
     * @ORM\Column(type="string", length=80)
     */
    protected $produktynullname;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $cenaProduktu;

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
     * @ORM\ManyToOne(targetEntity="Infogold\KlienciBundle\Entity\Faktura", inversedBy="sprzedaz", cascade={"persist"} )
     * @ORM\JoinColumn(name="zakup_id", referencedColumnName="id",nullable=true)
     */
    protected $zakup;
    
    
       /**
     * @ORM\ManyToOne(targetEntity="Infogold\KlienciBundle\Entity\Faktura", inversedBy="fakturaproforma", cascade={"persist"} )
     * @ORM\JoinColumn(name="proforma_id", referencedColumnName="id",nullable=true)
     */
    protected $proforma;

    /**
     * Constructor
     */

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $cenabrutto;

    public function __construct() {

        $this->ProduktyKlienta = new ArrayCollection();
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
     * Set ilosc
     *
     * @param integer $ilosc
     * @return ProduktyKlienta
     */
    public function setIlosc($ilosc)
    {
        $this->ilosc = $ilosc;

        return $this;
    }

    /**
     * Get ilosc
     *
     * @return integer 
     */
    public function getIlosc()
    {
        return $this->ilosc;
    }

    /**
     * Set cenaProduktu
     *
     * @param string $cenaProduktu
     * @return ProduktyKlienta
     */
    public function setCenaProduktu($cenaProduktu)
    {
        $this->cenaProduktu = $cenaProduktu;

        return $this;
    }

    /**
     * Get cenaProduktu
     *
     * @return string 
     */
    public function getCenaProduktu()
    {
        return $this->cenaProduktu;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ProduktyKlienta
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
     * @return ProduktyKlienta
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
     * Set cenabrutto
     *
     * @param string $cenabrutto
     * @return ProduktyKlienta
     */
    public function setCenabrutto($cenabrutto)
    {
        $this->cenabrutto = $cenabrutto;

        return $this;
    }

    /**
     * Get cenabrutto
     *
     * @return string 
     */
    public function getCenabrutto()
    {
        return $this->cenabrutto;
    }

    /**
     * Add ProduktyKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $produktyKlienta
     * @return ProduktyKlienta
     */
    public function addProduktyKlienta(\Infogold\KlienciBundle\Entity\Klienci $produktyKlienta)
    {
        $this->ProduktyKlienta[] = $produktyKlienta;

        return $this;
    }

    /**
     * Remove ProduktyKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $produktyKlienta
     */
    public function removeProduktyKlienta(\Infogold\KlienciBundle\Entity\Klienci $produktyKlienta)
    {
        $this->ProduktyKlienta->removeElement($produktyKlienta);
    }

    /**
     * Get ProduktyKlienta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProduktyKlienta()
    {
        return $this->ProduktyKlienta;
    }

    /**
     * Set produkty
     *
     * @param \Infogold\AccountBundle\Entity\Produkt $produkty
     * @return ProduktyKlienta
     */
    public function setProdukty(\Infogold\AccountBundle\Entity\Produkt $produkty = null)
    {
        $this->produkty = $produkty;

        return $this;
    }

    /**
     * Get produkty
     *
     * @return \Infogold\AccountBundle\Entity\Produkt 
     */
    public function getProdukty()
    {
        return $this->produkty;
    }

    /**
     * Set zakup
     *
     * @param \Infogold\KlienciBundle\Entity\Faktura $zakup
     * @return ProduktyKlienta
     */
    public function setZakup(\Infogold\KlienciBundle\Entity\Faktura $zakup = null)
    {
        $this->zakup = $zakup;

        return $this;
    }

    /**
     * Get zakup
     *
     * @return \Infogold\KlienciBundle\Entity\Faktura 
     */
    public function getZakup()
    {
        return $this->zakup;
    }

    /**
     * Set proforma
     *
     * @param \Infogold\KlienciBundle\Entity\Faktura $proforma
     * @return ProduktyKlienta
     */
    public function setProforma(\Infogold\KlienciBundle\Entity\Faktura $proforma = null)
    {
        $this->proforma = $proforma;

        return $this;
    }

    /**
     * Get proforma
     *
     * @return \Infogold\KlienciBundle\Entity\Faktura 
     */
    public function getProforma()
    {
        return $this->proforma;
    }
    function getJednostkamiary() {
        return $this->jednostkamiary;
    }

    function getProduktynullname() {
        return $this->produktynullname;
    }

    function setJednostkamiary($jednostkamiary) {
        $this->jednostkamiary = $jednostkamiary;
    }

    function setProduktynullname($produktynullname) {
        $this->produktynullname = $produktynullname;
    }


}
