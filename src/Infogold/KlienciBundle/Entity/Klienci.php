<?php

namespace Infogold\KlienciBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="klienci")
 * @ORM\Entity()
 */
class Klienci {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=80, unique=false, nullable=true))
     */
    protected $numerklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $nazwaklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $telefonklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $emailklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $stronawwwklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $regonklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $nipklienta;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $peselklienta;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $imie;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $nazwisko;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $ulica;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $kodpocztowy;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $miasto;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $nrdomu;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $nrmieszkania;

    /**
     * @ORM\Column(type="string", length=80,  nullable=true)
     */
    protected $wojewodztwo;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    protected $poprzednikonsultant;

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
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Faktura", mappedBy="dlaklienta",cascade={"persist", "remove"})
     */
    protected $faktura;

    /**
     * @ORM\ManyToOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="KonsultantKlienta")
      @ORM\JoinColumn(name="KlientKonsultanta_id", referencedColumnName="id",  nullable=true)
     */
    protected $KlientKonsultanta;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Kontakty", mappedBy="Klient",  cascade={"persist", "remove"})
     */
    protected $KontaktKlienta;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Interakcja", mappedBy="Klient",  cascade={"persist", "remove"})
     */
    protected $InterakcjaKlienta;

    /**
     * @ORM\ManytoOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="klienci")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Infogold\KlienciBundle\Entity\ProduktyKlienta", mappedBy="ProduktyKlienta",  cascade={"persist", "remove"})
     * */
    private $KlientaProdukty;

    /**
     * Constructor
     */
    public function __construct() {
        $this->faktura = new ArrayCollection();
        $this->KontaktKlienta = new ArrayCollection();
        $this->KlientaProdukty = new ArrayCollection();
        $this->InterakcjaKlienta = new ArrayCollection();
    }

    public function __toString() {
        if (!$this->getPeselklienta()) {
            return $this->getNazwaklienta() . " NIP: " . $this->getNipklienta();
        } else {
            return $this->getImie() . " " . $this->getNazwisko() . " PESEL: " . $this->getPeselklienta();
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set numerklienta
     *
     * @param string $numerklienta
     * @return Klienci
     */
    public function setNumerklienta($numerklienta) {
        $this->numerklienta = $numerklienta;

        return $this;
    }

    /**
     * Get numerklienta
     *
     * @return string 
     */
    public function getNumerklienta() {
        return $this->numerklienta;
    }

    /**
     * Set nazwaklienta
     *
     * @param string $nazwaklienta
     * @return Klienci
     */
    public function setNazwaklienta($nazwaklienta) {
        $this->nazwaklienta = $nazwaklienta;

        return $this;
    }

    /**
     * Get nazwaklienta
     *
     * @return string 
     */
    public function getNazwaklienta() {
        return $this->nazwaklienta;
    }

    /**
     * Set telefonklienta
     *
     * @param string $telefonklienta
     * @return Klienci
     */
    public function setTelefonklienta($telefonklienta) {
        $this->telefonklienta = $telefonklienta;

        return $this;
    }

    /**
     * Get telefonklienta
     *
     * @return string 
     */
    public function getTelefonklienta() {
        return $this->telefonklienta;
    }

    /**
     * Set emailklienta
     *
     * @param string $emailklienta
     * @return Klienci
     */
    public function setEmailklienta($emailklienta) {
        $this->emailklienta = $emailklienta;

        return $this;
    }

    /**
     * Get emailklienta
     *
     * @return string 
     */
    public function getEmailklienta() {
        return $this->emailklienta;
    }

    /**
     * Set stronawwwklienta
     *
     * @param string $stronawwwklienta
     * @return Klienci
     */
    public function setStronawwwklienta($stronawwwklienta) {
        $this->stronawwwklienta = $stronawwwklienta;

        return $this;
    }

    /**
     * Get stronawwwklienta
     *
     * @return string 
     */
    public function getStronawwwklienta() {
        return $this->stronawwwklienta;
    }

    /**
     * Set regonklienta
     *
     * @param string $regonklienta
     * @return Klienci
     */
    public function setRegonklienta($regonklienta) {
        $this->regonklienta = $regonklienta;

        return $this;
    }

    /**
     * Get regonklienta
     *
     * @return string 
     */
    public function getRegonklienta() {
        return $this->regonklienta;
    }

    /**
     * Set nipklienta
     *
     * @param string $nipklienta
     * @return Klienci
     */
    public function setNipklienta($nipklienta) {
        $this->nipklienta = $nipklienta;

        return $this;
    }

    /**
     * Get nipklienta
     *
     * @return string 
     */
    public function getNipklienta() {
        return $this->nipklienta;
    }

    /**
     * Set peselklienta
     *
     * @param string $peselklienta
     * @return Klienci
     */
    public function setPeselklienta($peselklienta) {
        $this->peselklienta = $peselklienta;

        return $this;
    }

    /**
     * Get peselklienta
     *
     * @return string 
     */
    public function getPeselklienta() {
        return $this->peselklienta;
    }

    /**
     * Set imie
     *
     * @param string $imie
     * @return Klienci
     */
    public function setImie($imie) {
        $this->imie = $imie;

        return $this;
    }

    /**
     * Get imie
     *
     * @return string 
     */
    public function getImie() {
        return $this->imie;
    }

    /**
     * Set nazwisko
     *
     * @param string $nazwisko
     * @return Klienci
     */
    public function setNazwisko($nazwisko) {
        $this->nazwisko = $nazwisko;

        return $this;
    }

    /**
     * Get nazwisko
     *
     * @return string 
     */
    public function getNazwisko() {
        return $this->nazwisko;
    }

    /**
     * Set ulica
     *
     * @param string $ulica
     * @return Klienci
     */
    public function setUlica($ulica) {
        $this->ulica = $ulica;

        return $this;
    }

    /**
     * Get ulica
     *
     * @return string 
     */
    public function getUlica() {
        return $this->ulica;
    }

    /**
     * Set kodpocztowy
     *
     * @param string $kodpocztowy
     * @return Klienci
     */
    public function setKodpocztowy($kodpocztowy) {
        $this->kodpocztowy = $kodpocztowy;

        return $this;
    }

    /**
     * Get kodpocztowy
     *
     * @return string 
     */
    public function getKodpocztowy() {
        return $this->kodpocztowy;
    }

    /**
     * Set miasto
     *
     * @param string $miasto
     * @return Klienci
     */
    public function setMiasto($miasto) {
        $this->miasto = $miasto;

        return $this;
    }

    /**
     * Get miasto
     *
     * @return string 
     */
    public function getMiasto() {
        return $this->miasto;
    }

    /**
     * Set nrdomu
     *
     * @param string $nrdomu
     * @return Klienci
     */
    public function setNrdomu($nrdomu) {
        $this->nrdomu = $nrdomu;

        return $this;
    }

    /**
     * Get nrdomu
     *
     * @return string 
     */
    public function getNrdomu() {
        return $this->nrdomu;
    }

    /**
     * Set nrmieszkania
     *
     * @param string $nrmieszkania
     * @return Klienci
     */
    public function setNrmieszkania($nrmieszkania) {
        $this->nrmieszkania = $nrmieszkania;

        return $this;
    }

    /**
     * Get nrmieszkania
     *
     * @return string 
     */
    public function getNrmieszkania() {
        return $this->nrmieszkania;
    }

    /**
     * Set wojewodztwo
     *
     * @param string $wojewodztwo
     * @return Klienci
     */
    public function setWojewodztwo($wojewodztwo) {
        $this->wojewodztwo = $wojewodztwo;

        return $this;
    }

    /**
     * Get wojewodztwo
     *
     * @return string 
     */
    public function getWojewodztwo() {
        return $this->wojewodztwo;
    }

    /**
     * Set poprzednikonsultant
     *
     * @param string $poprzednikonsultant
     * @return Klienci
     */
    public function setPoprzednikonsultant($poprzednikonsultant) {
        $this->poprzednikonsultant = $poprzednikonsultant;

        return $this;
    }

    /**
     * Get poprzednikonsultant
     *
     * @return string 
     */
    public function getPoprzednikonsultant() {
        return $this->poprzednikonsultant;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Klienci
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Klienci
     */
    public function setUpdated($updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * Set KlientKonsultanta
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $klientKonsultanta
     * @return Klienci
     */
    public function setKlientKonsultanta(\Infogold\KonsultantBundle\Entity\Konsultant $klientKonsultanta = null) {
        $this->KlientKonsultanta = $klientKonsultanta;

        return $this;
    }

    /**
     * Get KlientKonsultanta
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getKlientKonsultanta() {
        return $this->KlientKonsultanta;
    }

    /**
     * Remove KontaktKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Kontakty $kontaktKlienta
     */
    public function removeKontaktKlienta(\Infogold\KlienciBundle\Entity\Kontakty $kontaktKlienta) {
        $this->KontaktKlienta->removeElement($kontaktKlienta);
    }

    /**
     * Get KontaktKlienta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKontaktKlienta() {
        return $this->KontaktKlienta;
    }

    /**
     * Get InterakcjaKlienta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterakcjaKlienta() {
        return $this->InterakcjaKlienta;
    }

    /**
     * Add KontaktKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Kontakty $kontaktKlienta
     * @return Klienci
     */
    public function addKontaktKlienta(\Infogold\KlienciBundle\Entity\Kontakty $kontaktKlienta) {
        $this->KontaktKlienta[] = $kontaktKlienta;

        return $this;
    }

    /**
     * Add InterakcjaKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Interakcja $interakcjaKlienta
     * @return Klienci
     */
    public function addInterakcjaKlienta(\Infogold\KlienciBundle\Entity\Interakcja $interakcjaKlienta) {
        $this->InterakcjaKlienta[] = $interakcjaKlienta;

        return $this;
    }

    /**
     * Remove InterakcjaKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Interakcja $interakcjaKlienta
     */
    public function removeInterakcjaKlienta(\Infogold\KlienciBundle\Entity\Interakcja $interakcjaKlienta) {
        $this->InterakcjaKlienta->removeElement($interakcjaKlienta);
    }

    /**
     * Set user
     *
     * @param \Infogold\UserBundle\Entity\User $user
     * @return Klienci
     */
    public function setUser(\Infogold\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Infogold\UserBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add KlientaProdukty
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $klientaProdukty
     * @return Klienci
     */
    public function addKlientaProdukty(\Infogold\KlienciBundle\Entity\ProduktyKlienta $klientaProdukty) {
        $this->KlientaProdukty[] = $klientaProdukty;

        return $this;
    }

    /**
     * Remove KlientaProdukty
     *
     * @param \Infogold\KlienciBundle\Entity\ProduktyKlienta $klientaProdukty
     */
    public function removeKlientaProdukty(\Infogold\KlienciBundle\Entity\ProduktyKlienta $klientaProdukty) {
        $this->KlientaProdukty->removeElement($klientaProdukty);
    }

    /**
     * Get KlientaProdukty
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKlientaProdukty() {
        return $this->KlientaProdukty;
    }

    /**
     * Add faktura
     *
     * @param \Infogold\KlienciBundle\Entity\Faktura $faktura
     * @return Klienci
     */
    public function addFaktura(\Infogold\KlienciBundle\Entity\Faktura $faktura) {
        $this->faktura[] = $faktura;

        return $this;
    }

    /**
     * Remove faktura
     *
     * @param \Infogold\KlienciBundle\Entity\Faktura $faktura
     */
    public function removeFaktura(\Infogold\KlienciBundle\Entity\Faktura $faktura) {
        $this->faktura->removeElement($faktura);
    }

    /**
     * Get faktura
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFaktura() {
        return $this->faktura;
    }

}
