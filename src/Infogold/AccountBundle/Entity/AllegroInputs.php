<?php

namespace Infogold\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Infogold\AccountBundle\Entity\AllegroInputs
 * @ORM\Entity
 * @ORM\Table(name="AllegroInputs")
 */
class AllegroInputs {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

        /**
     * One Cart has One Customer.
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="allegroInputs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    protected $user;
    
    /**
     * One Cart has One Customer.
     * @ORM\ManyToOne(targetEntity="Infogold\AccountBundle\Entity\Produkt", inversedBy="allegroInput")
     * @ORM\JoinColumn(name="produkt_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $produkt;
    
        /**
     * One Cart has One Customer.
     * @ORM\ManyToOne(targetEntity="Infogold\AccountBundle\Entity\Category", inversedBy="allegroInput")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;
    
        /**
     * @ORM\Column(type="integer")
     */
    protected $formId;
    
     /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $typeField;
    
    
     /**
     * @ORM\Column(type="string", length=1000))
     */
    protected $value;
   
    

    
    

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
     * Set formId
     *
     * @param integer $formId
     *
     * @return AllegroInputs
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;

        return $this;
    }

    /**
     * Get formId
     *
     * @return integer
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * Set typeField
     *
     * @param integer $typeField
     *
     * @return AllegroInputs
     */
    public function setTypeField($typeField)
    {
        $this->typeField = $typeField;

        return $this;
    }

    /**
     * Get typeField
     *
     * @return integer
     */
    public function getTypeField()
    {
        return $this->typeField;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return AllegroInputs
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \Infogold\UserBundle\Entity\User $user
     *
     * @return AllegroInputs
     */
    public function setUser(\Infogold\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Infogold\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set produkt
     *
     * @param \Infogold\AccountBundle\Entity\Produkt $produkt
     *
     * @return AllegroInputs
     */
    public function setProdukt(\Infogold\AccountBundle\Entity\Produkt $produkt = null)
    {
        $this->produkt = $produkt;

        return $this;
    }

    /**
     * Get produkt
     *
     * @return \Infogold\AccountBundle\Entity\Produkt
     */
    public function getProdukt()
    {
        return $this->produkt;
    }

    /**
     * Set category
     *
     * @param \Infogold\AccountBundle\Entity\Category $category
     *
     * @return AllegroInputs
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
