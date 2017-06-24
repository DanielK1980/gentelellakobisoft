<?php

namespace Infogold\KonsultantBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity()
 */
class Role implements RoleInterface {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
     /**
     * @ORM\Column(name="name", type="string", length=30)
     */
    protected $name;
    /**
     * @ORM\Column(name="Roles", type="string", length=30)
     */
    protected $Roles;

    /**
     * @ORM\OneToMany(targetEntity="Konsultant", mappedBy="KonsultantRoles")
     */
    protected $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    // ... getters and setters for each property

    public function __toString(){
        
        return $this->getName();
        
    }

    public function getRole(){
        
        return $this->getRoles();
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
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add users
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $users
     * @return Role
     */
    public function addUsers(\Infogold\KonsultantBundle\Entity\Konsultant $users) {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $users
     */
    public function removeUsers(\Infogold\KonsultantBundle\Entity\Konsultant $users) {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers() {
        return $this->users;
    }
    public function getRoles() {
        return $this->Roles;
    }

    public function setRoles($Roles) {
        $this->Roles = $Roles;
    }




    /**
     * Add users
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $users
     * @return Role
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
}
