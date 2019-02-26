<?php

// src/Acme/UserBundle/Entity/User.php

namespace Infogold\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Infogold\UserBundle\Entity\User
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $Nrklienta;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $imie;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $nazwisko;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $telefon;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $nazwafirmy;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $kodpocztowy;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $miejscowosc;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $ulica;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $nip;


    /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", mappedBy="firma")
     */
    protected $konsultanci;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\Dzialy", mappedBy="DzialyFirmy")
     */
    protected $dzialy;
    
        /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\Category", mappedBy="company")
     */
    protected $category;
    
            /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\RodzajeKontaktow", mappedBy="company")
     */
    protected $contacttype;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Klienci", mappedBy="user")
     */
    protected $klienci;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\Produkt", mappedBy="userproduktu")
     */
    protected $produkty;
    
     /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Faktura", mappedBy="userfaktury")
     */
    protected $faktury;
    
    /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\AllegroInputs", mappedBy="user")
     */
    protected $allegroInputs;
    
        /**
     * One Customer has One Cart.
     * @ORM\OneToOne(targetEntity="Infogold\AccountBundle\Entity\Allegro", mappedBy="UserAllegro")
     */
    protected $allegro;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $nrkonta;
    
         /**
     * @var \DateTime $enableActivate
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="enabled", value="true")
     */
    private $enableActivate;

    
     /**
     * @var \DateTime $enableDeactivate
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="enabled", value="false")
     */
    private $enableDeactivate;
          /**
     * @ORM\Column(type="boolean")
     */
    protected $locked;
    
              /**
     * @ORM\Column(type="boolean")
     */
    protected $enableAllegro;
    
             /**
     * @var \DateTime $enableAllegro_at
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="enableAllegro", value="false")
     */
    private $enableAllegro_at;

         /**
     * @var \DateTime $locked_at
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="locked", value="false")
     */
    private $locked_at;
    
       /**
     * @ORM\Column(type="boolean")
     */
    protected $enableMagazyn;
    
    
    public function __construct() {
        parent::__construct();
        $this->konsultanci = new ArrayCollection();
        $this->dzialy = new ArrayCollection();
        $this->klienci = new ArrayCollection();
        $this->produkty = new ArrayCollection();
        $this->faktury = new ArrayCollection();
    }

    /**
     * Set Nrklienta
     *
     * @param integer $nrklienta
     * @return User
     */
    public function setNrklienta($nrklienta)
    {
        $this->Nrklienta = $nrklienta;

        return $this;
    }

    /**
     * Get Nrklienta
     *
     * @return integer 
     */
    public function getNrklienta()
    {
        return $this->Nrklienta;
    }

    /**
     * Set imie
     *
     * @param string $imie
     * @return User
     */
    public function setImie($imie)
    {
        $this->imie = $imie;

        return $this;
    }

    /**
     * Get imie
     *
     * @return string 
     */
    public function getImie()
    {
        return $this->imie;
    }

    /**
     * Set nazwisko
     *
     * @param string $nazwisko
     * @return User
     */
    public function setNazwisko($nazwisko)
    {
        $this->nazwisko = $nazwisko;

        return $this;
    }

    /**
     * Get nazwisko
     *
     * @return string 
     */
    public function getNazwisko()
    {
        return $this->nazwisko;
    }

    /**
     * Set telefon
     *
     * @param string $telefon
     * @return User
     */
    public function setTelefon($telefon)
    {
        $this->telefon = $telefon;

        return $this;
    }

    /**
     * Get telefon
     *
     * @return string 
     */
    public function getTelefon()
    {
        return $this->telefon;
    }

    /**
     * Set nazwafirmy
     *
     * @param string $nazwafirmy
     * @return User
     */
    public function setNazwafirmy($nazwafirmy)
    {
        $this->nazwafirmy = $nazwafirmy;

        return $this;
    }

    /**
     * Get nazwafirmy
     *
     * @return string 
     */
    public function getNazwafirmy()
    {
        return $this->nazwafirmy;
    }

    /**
     * Set kodpocztowy
     *
     * @param string $kodpocztowy
     * @return User
     */
    public function setKodpocztowy($kodpocztowy)
    {
        $this->kodpocztowy = $kodpocztowy;

        return $this;
    }

    /**
     * Get kodpocztowy
     *
     * @return string 
     */
    public function getKodpocztowy()
    {
        return $this->kodpocztowy;
    }

    /**
     * Set miejscowosc
     *
     * @param string $miejscowosc
     * @return User
     */
    public function setMiejscowosc($miejscowosc)
    {
        $this->miejscowosc = $miejscowosc;

        return $this;
    }

    /**
     * Get miejscowosc
     *
     * @return string 
     */
    public function getMiejscowosc()
    {
        return $this->miejscowosc;
    }

    /**
     * Set ulica
     *
     * @param string $ulica
     * @return User
     */
    public function setUlica($ulica)
    {
        $this->ulica = $ulica;

        return $this;
    }

    /**
     * Get ulica
     *
     * @return string 
     */
    public function getUlica()
    {
        return $this->ulica;
    }

    /**
     * Set nip
     *
     * @param string $nip
     * @return User
     */
    public function setNip($nip)
    {
        $this->nip = $nip;

        return $this;
    }

    /**
     * Get nip
     *
     * @return string 
     */
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * Set nrkonta
     *
     * @param string $nrkonta
     * @return User
     */
    public function setNrkonta($nrkonta)
    {
        $this->nrkonta = $nrkonta;

        return $this;
    }

    /**
     * Get nrkonta
     *
     * @return string 
     */
    public function getNrkonta()
    {
        return $this->nrkonta;
    }

    /**
     * Set enableActivate
     *
     * @param \DateTime $enableActivate
     * @return User
     */
    public function setEnableActivate($enableActivate)
    {
        $this->enableActivate = $enableActivate;

        return $this;
    }

    /**
     * Get enableActivate
     *
     * @return \DateTime 
     */
    public function getEnableActivate()
    {
        return $this->enableActivate;
    }

    /**
     * Set enableDeactivate
     *
     * @param \DateTime $enableDeactivate
     * @return User
     */
    public function setEnableDeactivate($enableDeactivate)
    {
        $this->enableDeactivate = $enableDeactivate;

        return $this;
    }

    /**
     * Get enableDeactivate
     *
     * @return \DateTime 
     */
    public function getEnableDeactivate()
    {
        return $this->enableDeactivate;
    }

 

    /**
     * Set enableMagazyn
     *
     * @param boolean $enableMagazyn
     * @return User
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
     * Add konsultanci
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $konsultanci
     * @return User
     */
    public function addKonsultanci(\Infogold\KonsultantBundle\Entity\Konsultant $konsultanci)
    {
        $this->konsultanci[] = $konsultanci;

        return $this;
    }

    /**
     * Remove konsultanci
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $konsultanci
     */
    public function removeKonsultanci(\Infogold\KonsultantBundle\Entity\Konsultant $konsultanci)
    {
        $this->konsultanci->removeElement($konsultanci);
    }

    /**
     * Get konsultanci
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKonsultanci()
    {
        return $this->konsultanci;
    }

    /**
     * Add dzialy
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $dzialy
     * @return User
     */
    public function addDzialy(\Infogold\AccountBundle\Entity\Dzialy $dzialy)
    {
        $this->dzialy[] = $dzialy;

        return $this;
    }

    /**
     * Remove dzialy
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $dzialy
     */
    public function removeDzialy(\Infogold\AccountBundle\Entity\Dzialy $dzialy)
    {
        $this->dzialy->removeElement($dzialy);
    }

    /**
     * Get dzialy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDzialy()
    {
        return $this->dzialy;
    }

    /**
     * Add category
     *
     * @param \Infogold\AccountBundle\Entity\Category $category
     * @return User
     */
    public function addCategory(\Infogold\AccountBundle\Entity\Category $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Infogold\AccountBundle\Entity\Category $category
     */
    public function removeCategory(\Infogold\AccountBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add contacttype
     *
     * @param \Infogold\KlienciBundle\Entity\RodzajeKontaktow $contacttype
     * @return User
     */
    public function addContacttype(\Infogold\KlienciBundle\Entity\RodzajeKontaktow $contacttype)
    {
        $this->contacttype[] = $contacttype;

        return $this;
    }

    /**
     * Remove contacttype
     *
     * @param \Infogold\KlienciBundle\Entity\RodzajeKontaktow $contacttype
     */
    public function removeContacttype(\Infogold\KlienciBundle\Entity\RodzajeKontaktow $contacttype)
    {
        $this->contacttype->removeElement($contacttype);
    }

    /**
     * Get contacttype
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacttype()
    {
        return $this->contacttype;
    }

    /**
     * Add klienci
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $klienci
     * @return User
     */
    public function addKlienci(\Infogold\KlienciBundle\Entity\Klienci $klienci)
    {
        $this->klienci[] = $klienci;

        return $this;
    }

    /**
     * Remove klienci
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $klienci
     */
    public function removeKlienci(\Infogold\KlienciBundle\Entity\Klienci $klienci)
    {
        $this->klienci->removeElement($klienci);
    }

    /**
     * Get klienci
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKlienci()
    {
        return $this->klienci;
    }

    /**
     * Add produkty
     *
     * @param \Infogold\AccountBundle\Entity\Produkt $produkty
     * @return User
     */
    public function addProdukty(\Infogold\AccountBundle\Entity\Produkt $produkty)
    {
        $this->produkty[] = $produkty;

        return $this;
    }

    /**
     * Remove produkty
     *
     * @param \Infogold\AccountBundle\Entity\Produkt $produkty
     */
    public function removeProdukty(\Infogold\AccountBundle\Entity\Produkt $produkty)
    {
        $this->produkty->removeElement($produkty);
    }

    /**
     * Get produkty
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProdukty()
    {
        return $this->produkty;
    }

    /**
     * Add faktury
     *
     * @param \Infogold\KlienciBundle\Entity\Faktura $faktury
     * @return User
     */
    public function addFaktury(\Infogold\KlienciBundle\Entity\Faktura $faktury)
    {
        $this->faktury[] = $faktury;

        return $this;
    }

    /**
     * Remove faktury
     *
     * @param \Infogold\KlienciBundle\Entity\Faktura $faktury
     */
    public function removeFaktury(\Infogold\KlienciBundle\Entity\Faktura $faktury)
    {
        $this->faktury->removeElement($faktury);
    }

    /**
     * Get faktury
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFaktury()
    {
        return $this->faktury;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set lockedat
     *
     * @param \DateTime $locked_at
     *
     * @return User
     */
    public function setLocked_at($locked_at)
    {
        $this->locked_at = $locked_at;

        return $this;
    }

    /**
     * Get locked_at
     *
     * @return \DateTime
     */
    public function getLocked_at()
    {
        return $this->locked_at;
    }

    /**
     * Set enableAllegro
     *
     * @param boolean $enableAllegro
     *
     * @return User
     */
    public function setEnableAllegro($enableAllegro)
    {
        $this->enableAllegro = $enableAllegro;

        return $this;
    }

    /**
     * Get enableAllegro
     *
     * @return boolean
     */
    public function getEnableAllegro()
    {
        return $this->enableAllegro;
    }

    /**
     * Set enableAllegroAt
     *
     * @param \DateTime $enableAllegroAt
     *
     * @return User
     */
    public function setEnableAllegroAt($enableAllegroAt)
    {
        $this->enableAllegro_at = $enableAllegroAt;

        return $this;
    }

    /**
     * Get enableAllegroAt
     *
     * @return \DateTime
     */
    public function getEnableAllegroAt()
    {
        return $this->enableAllegro_at;
    }

    /**
     * Set lockedAt
     *
     * @param \DateTime $lockedAt
     *
     * @return User
     */
    public function setLockedAt($lockedAt)
    {
        $this->locked_at = $lockedAt;

        return $this;
    }

    /**
     * Get lockedAt
     *
     * @return \DateTime
     */
    public function getLockedAt()
    {
        return $this->locked_at;
    }



    /**
     * Set allegro
     *
     * @param \Infogold\AccountBundle\Entity\Allegro $allegro
     *
     * @return User
     */
    public function setAllegro(\Infogold\AccountBundle\Entity\Allegro $allegro = null)
    {
        $this->allegro = $allegro;

        return $this;
    }

    /**
     * Get allegro
     *
     * @return \Infogold\AccountBundle\Entity\Allegro
     */
    public function getAllegro()
    {
        return $this->allegro;
    }

    /**
     * Add allegroInput
     *
     * @param \Infogold\AccountBundle\Entity\AllegroInputs $allegroInput
     *
     * @return User
     */
    public function addAllegroInput(\Infogold\AccountBundle\Entity\AllegroInputs $allegroInput)
    {
        $this->allegroInputs[] = $allegroInput;

        return $this;
    }

    /**
     * Remove allegroInput
     *
     * @param \Infogold\AccountBundle\Entity\AllegroInputs $allegroInput
     */
    public function removeAllegroInput(\Infogold\AccountBundle\Entity\AllegroInputs $allegroInput)
    {
        $this->allegroInputs->removeElement($allegroInput);
    }

    /**
     * Get allegroInputs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllegroInputs()
    {
        return $this->allegroInputs;
    }
}
