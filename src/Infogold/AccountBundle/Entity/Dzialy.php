<?php

namespace Infogold\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="dzialy")
 * @ORM\Entity()
 */
class Dzialy {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", mappedBy="KonsultantDzialy")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\Kolejki", mappedBy="KolejkaDzialu")
     */
    protected $DzialyKolejki;

    /**
     * @ORM\OneToMany(targetEntity="Infogold\KonsultantBundle\Entity\Przerwy", mappedBy="PrzerwaDzialu")
     */
    protected $DzialyPrzerwy;
    
     /**
     * @ORM\OneToMany(targetEntity="Infogold\AccountBundle\Entity\Grafik", mappedBy="GrafikDzialy")
     */
    protected $grafik;

    /**
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="dzialy")
     * @ORM\JoinColumn(name="DzialyFirmy_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $DzialyFirmy;
    
     /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="department")
     */
    protected $category;
    /**
     * @ORM\Column(type="integer")
     */
    protected $limityprzerw;

    public function __construct() {

        $this->users = new ArrayCollection();
        $this->DzialyPrzerwy = new ArrayCollection();
        $this->DzialyKolejki = new ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return Dzialy
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
     * Set limityprzerw
     *
     * @param integer $limityprzerw
     * @return Dzialy
     */
    public function setLimityprzerw($limityprzerw)
    {
        $this->limityprzerw = $limityprzerw;

        return $this;
    }

    /**
     * Get limityprzerw
     *
     * @return integer 
     */
    public function getLimityprzerw()
    {
        return $this->limityprzerw;
    }

    /**
     * Add users
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $users
     * @return Dzialy
     */
    public function addUser(\Infogold\KonsultantBundle\Entity\Konsultant $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $users
     */
    public function removeUser(\Infogold\KonsultantBundle\Entity\Konsultant $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add DzialyKolejki
     *
     * @param \Infogold\KonsultantBundle\Entity\Kolejki $dzialyKolejki
     * @return Dzialy
     */
    public function addDzialyKolejki(\Infogold\KonsultantBundle\Entity\Kolejki $dzialyKolejki)
    {
        $this->DzialyKolejki[] = $dzialyKolejki;

        return $this;
    }

    /**
     * Remove DzialyKolejki
     *
     * @param \Infogold\KonsultantBundle\Entity\Kolejki $dzialyKolejki
     */
    public function removeDzialyKolejki(\Infogold\KonsultantBundle\Entity\Kolejki $dzialyKolejki)
    {
        $this->DzialyKolejki->removeElement($dzialyKolejki);
    }

    /**
     * Get DzialyKolejki
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDzialyKolejki()
    {
        return $this->DzialyKolejki;
    }

    /**
     * Add DzialyPrzerwy
     *
     * @param \Infogold\KonsultantBundle\Entity\Przerwy $dzialyPrzerwy
     * @return Dzialy
     */
    public function addDzialyPrzerwy(\Infogold\KonsultantBundle\Entity\Przerwy $dzialyPrzerwy)
    {
        $this->DzialyPrzerwy[] = $dzialyPrzerwy;

        return $this;
    }

    /**
     * Remove DzialyPrzerwy
     *
     * @param \Infogold\KonsultantBundle\Entity\Przerwy $dzialyPrzerwy
     */
    public function removeDzialyPrzerwy(\Infogold\KonsultantBundle\Entity\Przerwy $dzialyPrzerwy)
    {
        $this->DzialyPrzerwy->removeElement($dzialyPrzerwy);
    }

    /**
     * Get DzialyPrzerwy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDzialyPrzerwy()
    {
        return $this->DzialyPrzerwy;
    }

    /**
     * Add grafik
     *
     * @param \Infogold\AccountBundle\Entity\Grafik $grafik
     * @return Dzialy
     */
    public function addGrafik(\Infogold\AccountBundle\Entity\Grafik $grafik)
    {
        $this->grafik[] = $grafik;

        return $this;
    }

    /**
     * Remove grafik
     *
     * @param \Infogold\AccountBundle\Entity\Grafik $grafik
     */
    public function removeGrafik(\Infogold\AccountBundle\Entity\Grafik $grafik)
    {
        $this->grafik->removeElement($grafik);
    }

    /**
     * Get grafik
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGrafik()
    {
        return $this->grafik;
    }

    /**
     * Set DzialyFirmy
     *
     * @param \Infogold\UserBundle\Entity\User $dzialyFirmy
     * @return Dzialy
     */
    public function setDzialyFirmy(\Infogold\UserBundle\Entity\User $dzialyFirmy = null)
    {
        $this->DzialyFirmy = $dzialyFirmy;

        return $this;
    }

    /**
     * Get DzialyFirmy
     *
     * @return \Infogold\UserBundle\Entity\User 
     */
    public function getDzialyFirmy()
    {
        return $this->DzialyFirmy;
    }

    /**
     * Add category
     *
     * @param \Infogold\AccountBundle\Entity\Category $category
     * @return Dzialy
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
}
