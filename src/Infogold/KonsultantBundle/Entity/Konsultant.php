<?php

// src/UserBundle/Entity/Konsultant.php

namespace Infogold\KonsultantBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * Infogold\KonsultantBundle\Entity\Konsultant
 * @ORM\Entity
 * @ORM\Table(name="konsultant")
 * @ORM\Entity(repositoryClass="Infogold\KonsultantBundle\Entity\KonsultantRepository")
 */
class Konsultant implements UserInterface, \Serializable, EquatableInterface {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $imie;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $nazwisko;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $oldpassword;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="konsultanci")
     * @ORM\JoinColumn(name="firma_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $firma;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="KonsultantRoles_id", referencedColumnName="id",  onDelete="CASCADE"))
     */
    protected $KonsultantRoles;

    /**
     * @ORM\ManyToOne(targetEntity="Infogold\AccountBundle\Entity\Dzialy", inversedBy="users")
     * @ORM\JoinColumn(name="KonsultantDzialy_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $KonsultantDzialy;
    
   

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
     * @var datetime $updatePassword
     *
     * @ORM\Column(name="updatePassword", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"password"})
     */
    private $updatepassword;

        /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\CzasPracy", mappedBy="KonsultantaCzasy")
     */
    protected $KonsultantaCzasPracy;
    /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Klienci", mappedBy="KlientKonsultanta")
     */
    protected $KonsultantKlienta;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Kontakty", mappedBy="PrzypisanyDo")
     */
    protected $KontaktKonsultanta;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Interakcja", mappedBy="UtworzonyPrzez")
     */
    protected $InterakcjaKonsultanta;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\Kolejki", mappedBy="KonsultantKolejka")
     */
    protected $kolejka;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\Przerwy", mappedBy="KonsultantPrzerwa")
     */
    protected $przerwa;
    
    /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\RaportyPrzerw", mappedBy="KonsultantRaportPrzerw")
     */
    protected $raportprzerw;
    
       /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\Grafik", mappedBy="GrafikKonsultanta")
     */
    protected $KonsultantGrafik;
    
    

    public function __construct() {
        
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->kolejka = new ArrayCollection();
        $this->przerwa = new ArrayCollection();      
        $this->KonsultantKlienta = new ArrayCollection();
        $this->KontaktKonsultanta = new ArrayCollection();
        $this->InterakcjaKonsultanta = new ArrayCollection();
        $this->KonsultantaCzasPracy = new ArrayCollection();
    }

    public function __toString() {

        return $this->getImie() . " " . $this->getNazwisko();
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {

        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @inheritDoc
  
    public function getRoles() {
        $roles = array();

        $roles[] = $this->getKonsultantRoles();


        return $roles;
    }
*/
    
     public function getRoles()
    {
        return array($this->getKonsultantRoles()->getRoles());
    }
    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        return true;
    }



    public function isEnabled() {
        return $this->isActive;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
                $this->id,
                ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user) {
        return $this->id === $user->getId();
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
     * Set username
     *
     * @param string $username
     * @return Konsultant
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Konsultant
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Konsultant
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    public function getOldpassword() {
        return $this->oldpassword;
    }

    public function setOldpassword($oldpassword) {
        $this->oldpassword = $oldpassword;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Konsultant
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Konsultant
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive() {
        if ($this->isActive) {
            $converted_res = "tak";
        } else {
            $converted_res = "nie";
        }
        return $converted_res;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    /**
     * Set imie
     *
     * @param string $imie
     * @return Konsultant
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
     * @return Konsultant
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
     * Set created
     *
     * @param \DateTime $created
     * @return Konsultant
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Konsultant
     */
    public function setUpdated($updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Set updatepassword
     *
     * @param \DateTime $updatepassword
     * @return Konsultant
     */
    public function setUpdatepassword($updatepassword) {
        $this->updatepassword = $updatepassword;

        return $this;
    }

    /**
     * Get updatepassword
     *
     * @return \DateTime 
     */
    public function getUpdatepassword() {
        return $this->updatepassword;
    }

    /**
     * Set firma
     *
     * @param \Infogold\UserBundle\Entity\User $firma
     * @return Konsultant
     */
    public function setFirma(\Infogold\UserBundle\Entity\User $firma = null) {
        $this->firma = $firma;

        return $this;
    }

    /**
     * Get firma
     *
     * @return \Infogold\UserBundle\Entity\User 
     */
    public function getFirma() {
        return $this->firma;
    }

    /**
     * Set KonsultantRoles
     *
     * @param \Infogold\KonsultantBundle\Entity\Role $konsultantRoles
     * @return Konsultant
     */
    public function setKonsultantRoles(\Infogold\KonsultantBundle\Entity\Role $konsultantRoles = null) {
        $this->KonsultantRoles = $konsultantRoles;

        return $this;
    }

    /**
     * Get KonsultantRoles
     *
     * @return \Infogold\KonsultantBundle\Entity\Role 
     */
    public function getKonsultantRoles() {
        return $this->KonsultantRoles;
    }

    /**
     * Set KonsultantDzialy
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $konsultantDzialy
     * @return Konsultant
     */
    public function setKonsultantDzialy(\Infogold\AccountBundle\Entity\Dzialy $konsultantDzialy = null) {
        $this->KonsultantDzialy = $konsultantDzialy;

        return $this;
    }

    /**
     * Get KonsultantDzialy
     *
     * @return \Infogold\AccountBundle\Entity\Dzialy 
     */
    public function getKonsultantDzialy() {
        return $this->KonsultantDzialy;
    }

    /**
     * Add KonsultantKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $konsultantKlienta
     * @return Konsultant
     */
    public function addKonsultantKlienta(\Infogold\KlienciBundle\Entity\Klienci $konsultantKlienta) {
        $this->KonsultantKlienta[] = $konsultantKlienta;

        return $this;
    }

    /**
     * Remove KonsultantKlienta
     *
     * @param \Infogold\KlienciBundle\Entity\Klienci $konsultantKlienta
     */
    public function removeKonsultantKlienta(\Infogold\KlienciBundle\Entity\Klienci $konsultantKlienta) {
        $this->KonsultantKlienta->removeElement($konsultantKlienta);
    }

    /**
     * Get KonsultantKlienta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKonsultantKlienta() {
        return $this->KonsultantKlienta;
    }

    /**
     * Add KontaktKonsultanta
     *
     * @param \Infogold\KlienciBundle\Entity\Kontakty $kontaktKonsultanta
     * @return Konsultant
     */
    public function addKontaktKonsultanta(\Infogold\KlienciBundle\Entity\Kontakty $kontaktKonsultanta) {
        $this->KontaktKonsultanta[] = $kontaktKonsultanta;

        return $this;
    }

    /**
     * Remove KontaktKonsultanta
     *
     * @param \Infogold\KlienciBundle\Entity\Kontakty $kontaktKonsultanta
     */
    public function removeKontaktKonsultanta(\Infogold\KlienciBundle\Entity\Kontakty $kontaktKonsultanta) {
        $this->KontaktKonsultanta->removeElement($kontaktKonsultanta);
    }

    /**
     * Get KontaktKonsultanta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKontaktKonsultanta() {
        return $this->KontaktKonsultanta;
    }

    /**
     * Add InterakcjaKonsultanta
     *
     * @param \Infogold\KlienciBundle\Entity\Interakcja $interakcjaKonsultanta
     * @return Konsultant
     */
    public function addInterakcjaKonsultanta(\Infogold\KlienciBundle\Entity\Interakcja $interakcjaKonsultanta) {
        $this->InterakcjaKonsultanta[] = $interakcjaKonsultanta;

        return $this;
    }

    /**
     * Remove InterakcjaKonsultanta
     *
     * @param \Infogold\KlienciBundle\Entity\Interakcja $interakcjaKonsultanta
     */
    public function removeInterakcjaKonsultanta(\Infogold\KlienciBundle\Entity\Interakcja $interakcjaKonsultanta) {
        $this->InterakcjaKonsultanta->removeElement($interakcjaKonsultanta);
    }

    /**
     * Get InterakcjaKonsultanta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterakcjaKonsultanta() {
        return $this->InterakcjaKonsultanta;
    }

    /**
     * Add kolejka
     *
     * @param \Infogold\KonsultantBundle\Entity\Kolejki $kolejka
     * @return Konsultant
     */
    public function addKolejka(\Infogold\KonsultantBundle\Entity\Kolejki $kolejka) {
        $this->kolejka[] = $kolejka;

        return $this;
    }

    /**
     * Remove kolejka
     *
     * @param \Infogold\KonsultantBundle\Entity\Kolejki $kolejka
     */
    public function removeKolejka(\Infogold\KonsultantBundle\Entity\Kolejki $kolejka) {
        $this->kolejka->removeElement($kolejka);
    }

    /**
     * Get kolejka
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKolejka() {
        return $this->kolejka;
    }

    /**
     * Add przerwa
     *
     * @param \Infogold\KonsultantBundle\Entity\Przerwy $przerwa
     * @return Konsultant
     */
    public function addPrzerwa(\Infogold\KonsultantBundle\Entity\Przerwy $przerwa) {
        $this->przerwa[] = $przerwa;

        return $this;
    }

    /**
     * Remove przerwa
     *
     * @param \Infogold\KonsultantBundle\Entity\Przerwy $przerwa
     */
    public function removePrzerwa(\Infogold\KonsultantBundle\Entity\Przerwy $przerwa) {
        $this->przerwa->removeElement($przerwa);
    }

    /**
     * Get przerwa
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrzerwa() {
        return $this->przerwa;
    }
  


 

    /**
     * Add KonsultantaCzasPracy
     *
     * @param \Infogold\KonsultantBundle\Entity\CzasPracy $konsultantaCzasPracy
     * @return Konsultant
     */
    public function addKonsultantaCzasPracy(\Infogold\KonsultantBundle\Entity\CzasPracy $konsultantaCzasPracy)
    {
        $this->KonsultantaCzasPracy[] = $konsultantaCzasPracy;

        return $this;
    }

    /**
     * Remove KonsultantaCzasPracy
     *
     * @param \Infogold\KonsultantBundle\Entity\CzasPracy $konsultantaCzasPracy
     */
    public function removeKonsultantaCzasPracy(\Infogold\KonsultantBundle\Entity\CzasPracy $konsultantaCzasPracy)
    {
        $this->KonsultantaCzasPracy->removeElement($konsultantaCzasPracy);
    }

    /**
     * Get KonsultantaCzasPracy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKonsultantaCzasPracy()
    {
        return $this->KonsultantaCzasPracy;
    }

 

    /**
     * Add raportprzerw
     *
     * @param \Infogold\AccountBundle\Entity\RaportyPrzerw $raportprzerw
     * @return Konsultant
     */
    public function addRaportprzerw(\Infogold\AccountBundle\Entity\RaportyPrzerw $raportprzerw)
    {
        $this->raportprzerw[] = $raportprzerw;

        return $this;
    }

    /**
     * Remove raportprzerw
     *
     * @param \Infogold\AccountBundle\Entity\RaportyPrzerw $raportprzerw
     */
    public function removeRaportprzerw(\Infogold\AccountBundle\Entity\RaportyPrzerw $raportprzerw)
    {
        $this->raportprzerw->removeElement($raportprzerw);
    }

    /**
     * Get raportprzerw
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRaportprzerw()
    {
        return $this->raportprzerw;
    } 

    /**
     * Add konsultantGrafik
     *
     * @param \Infogold\AccountBundle\Entity\Grafik $konsultantGrafik
     *
     * @return Konsultant
     */
    public function addKonsultantGrafik(\Infogold\AccountBundle\Entity\Grafik $konsultantGrafik)
    {
        $this->KonsultantGrafik[] = $konsultantGrafik;

        return $this;
    }

    /**
     * Remove konsultantGrafik
     *
     * @param \Infogold\AccountBundle\Entity\Grafik $konsultantGrafik
     */
    public function removeKonsultantGrafik(\Infogold\AccountBundle\Entity\Grafik $konsultantGrafik)
    {
        $this->KonsultantGrafik->removeElement($konsultantGrafik);
    }

    /**
     * Get konsultantGrafik
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKonsultantGrafik()
    {
        return $this->KonsultantGrafik;
    }
}
