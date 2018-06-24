<?php

namespace Infogold\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="produkt")
 * @ORM\Entity()
 */
class Produkt {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="produkty")
     * @ORM\JoinColumn(name="userproduktu_id", referencedColumnName="id", onDelete="CASCADE") )
     */
    protected $userproduktu;
    
    
      /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\ProduktyKlienta", mappedBy="produkty")
     */
    protected $produkt;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $cenaProduktu;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $nrproduktu;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=180, nullable=true)
     */
    protected $opis;
    
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $htmlAllegro;
    
    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $itemIdAllegro;
       
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="produkty")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    protected $category;
    
     /**
     * @ORM\Column(type="decimal")
     */
    protected $vat;
    
     /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $cenabrutto;
    
    
      
      /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $magazyn;
    
         /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enableMagazyn;
    
       /**
     * @ORM\Column(type="string", length=80)
     */
    protected $jednostkamiary;

    /**
     * Constructor
     */
    public function __toString() {

        return $this->getName();
    }
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->produkt = new ArrayCollection();
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
     * Set cenaProduktu
     *
     * @param string $cenaProduktu
     * @return Produkt
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
     * Set nrproduktu
     *
     * @param string $nrproduktu
     * @return Produkt
     */
    public function setNrproduktu($nrproduktu)
    {
        $this->nrproduktu = $nrproduktu;

        return $this;
    }

    /**
     * Get nrproduktu
     *
     * @return string 
     */
    public function getNrproduktu()
    {
        return $this->nrproduktu;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Produkt
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set opis
     *
     * @param string $opis
     * @return Produkt
     */
    public function setOpis($opis)
    {
        $this->opis = $opis;

        return $this;
    }

    /**
     * Get opis
     *
     * @return string 
     */
    public function getOpis()
    {
        return $this->opis;
    }

    /**
     * Set vat
     *
     * @param string $vat
     * @return Produkt
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return string 
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set cenabrutto
     *
     * @param string $cenabrutto
     * @return Produkt
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
     * Set magazyn
     *
     * @param integer $magazyn
     * @return Produkt
     */
    public function setMagazyn($magazyn)
    {
        $this->magazyn = $magazyn;

        return $this;
    }

    /**
     * Get magazyn
     *
     * @return integer 
     */
    public function getMagazyn()
    {
        return $this->magazyn;
    }

    /**
     * Set enableMagazyn
     *
     * @param boolean $enableMagazyn
     * @return Produkt
     */
    public function setEnableMagazyn($enableMagazyn)
    {
        $this->enableMagazyn = $enableMagazyn;

        return $this;
    }

    /**
     * Get enableMagazyn
     *
     * @return boolean 
     */
    public function getEnableMagazyn()
    {
        return $this->enableMagazyn;
    }

    /**
     * Set jednostkamiary
     *
     * @param string $jednostkamiary
     * @return Produkt
     */
    public function setJednostkamiary($jednostkamiary)
    {
        $this->jednostkamiary = $jednostkamiary;

        return $this;
    }

    /**
     * Get htmlAllegro
     *
     * @return textarea 
     */
    public function getHtmlAllegro()
    {
        return $this->htmlAllegro;
    }
    
     /**
     * Set htmlAllegro
     *
     * @param textarea $htmlAllegro
     * @return HtmlAllegro
     */
    public function setHtmlAllegro($htmlAllegro)
    {
        $this->htmlAllegro = $htmlAllegro;

        return $this;
    }
    
        /**
     * Get itemIdAllegro
     *
     * @return decimal 
     */
    public function getItemIdAllegro()
    {
        return $this->itemIdAllegro;
    }
    
     /**
     * Set itemIdAllegro
     *
     * @param string $itemIdAllegro
     * @return ItemIdAllegro
     */
    public function setItemIdAllegro($itemIdAllegro)
    {
        $this->itemIdAllegro = $itemIdAllegro;

        return $this;
    }

    /**
     * Get jednostkamiary
     *
     * @return string 
     */
    public function getJednostkamiary()
    {
        return $this->jednostkamiary;
    }

    /**
     * Set userproduktu
     *
     * @param \Infogold\UserBundle\Entity\User $userproduktu
     * @return Produkt
     */
    public function setUserproduktu(\Infogold\UserBundle\Entity\User $userproduktu = null)
    {
        $this->userproduktu = $userproduktu;

        return $this;
    }

    /**
     * Get userproduktu
     *
     * @return \Infogold\UserBundle\Entity\User 
     */
    public function getUserproduktu()
    {
        return $this->userproduktu;
    }


    /**
     * Add produkt
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $produkt
     * @return Produkt
     */
    public function addProdukt(\Infogold\KlienciBundle\Entity\ProduktyKlienta $produkt)
    {
        $this->produkt[] = $produkt;

        return $this;
    }

    /**
     * Remove produkt
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $produkt
     */
    public function removeProdukt(\Infogold\KlienciBundle\Entity\ProduktyKlienta $produkt)
    {
        $this->produkt->removeElement($produkt);
    }

    /**
     * Get produkt
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProdukt()
    {
        return $this->produkt;
    }

    /**
     * Set category
     *
     * @param \Infogold\AccountBundle\Entity\Category $category
     * @return Produkt
     */
    public function setCategory(\Infogold\AccountBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Infogold\AccountBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
