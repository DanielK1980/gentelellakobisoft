<?php

namespace Infogold\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity()
 */
class Category {

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
     * @ORM\ManyToOne(targetEntity="Dzialy", inversedBy="category")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $department;
    
       /**
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="category")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $company;
   
     /**
     * @ORM\OneToMany(targetEntity="Produkt", mappedBy="category")
     */
    protected $produkty;
    
     public function __construct() {

        $this->produkty = new ArrayCollection();    
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
     * @return Category
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
     * Set department
     *
     * @param \Infogold\AccountBundle\Entity\Dzialy $department
     * @return Category
     */
    public function setDepartment(\Infogold\AccountBundle\Entity\Dzialy $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Infogold\AccountBundle\Entity\Dzialy 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set company
     *
     * @param \Infogold\UserBundle\Entity\User $company
     * @return Category
     */
    public function setCompany(\Infogold\UserBundle\Entity\User $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Infogold\UserBundle\Entity\User 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Add produkty
     *
     * @param \Infogold\AccountBundle\Entity\Produkt $produkty
     * @return Category
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
}
