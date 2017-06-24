<?php

namespace Infogold\KlienciBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rodzajekontaktow")
 * @ORM\Entity()
 */
class RodzajeKontaktow {

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
     * @ORM\OneToMany(targetEntity="Infogold\KlienciBundle\Entity\Kontakty", mappedBy="RodzajKontaktu")
     */
    protected $NazwaKontaktu;
    
    
         /**
     * @ORM\ManyToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="contacttype")
     * @ORM\JoinColumn(name="contacttype_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $company;
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->NazwaKontaktu = new ArrayCollection();
    }

    
      public function __toString(){
        
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
     * @return RodzajeKontaktow
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
     * Add NazwaKontaktu
     *
     * @param \Infogold\KlienciBundle\Entity\Kontakty $nazwaKontaktu
     * @return RodzajeKontaktow
     */
    public function addNazwaKontaktu(\Infogold\KlienciBundle\Entity\Kontakty $nazwaKontaktu)
    {
        $this->NazwaKontaktu[] = $nazwaKontaktu;

        return $this;
    }

    /**
     * Remove NazwaKontaktu
     *
     * @param \Infogold\KlienciBundle\Entity\Kontakty $nazwaKontaktu
     */
    public function removeNazwaKontaktu(\Infogold\KlienciBundle\Entity\Kontakty $nazwaKontaktu)
    {
        $this->NazwaKontaktu->removeElement($nazwaKontaktu);
    }

    /**
     * Get NazwaKontaktu
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNazwaKontaktu()
    {
        return $this->NazwaKontaktu;
    }

    /**
     * Set company
     *
     * @param \Infogold\UserBundle\Entity\User $company
     * @return RodzajeKontaktow
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
}
