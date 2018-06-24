<?php

namespace Infogold\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Infogold\AccountBundle\Entity\Allegro
 * @ORM\Entity
 * @ORM\Table(name="Allegro")
 */
class Allegro {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

        /**
     * One Cart has One Customer.
     * @ORM\OneToOne(targetEntity="Infogold\UserBundle\Entity\User", inversedBy="allegro")
     * @ORM\JoinColumn(name="user_allegro_id", referencedColumnName="id")
     */
    protected $User_Allegro;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $AllegroKeyWebApi;


     /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $LoginAllegro;
 

     /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $PasswordAllegro;
    
         /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $Iv;
    
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
     * Set allegroKeyWebApi
     *
     * @param string $allegroKeyWebApi
     *
     * @return Allegro
     */
    public function setAllegroKeyWebApi($allegroKeyWebApi)
    {
        $this->AllegroKeyWebApi = $allegroKeyWebApi;

        return $this;
    }

    /**
     * Get allegroKeyWebApi
     *
     * @return string
     */
    public function getAllegroKeyWebApi()
    {
        return $this->AllegroKeyWebApi;
    }

    /**
     * Set userAllegro
     *
     * @param \Infogold\UserBundle\Entity\User $userAllegro
     *
     * @return Allegro
     */
    public function setUserAllegro(\Infogold\UserBundle\Entity\User $userAllegro = null)
    {
        $this->User_Allegro = $userAllegro;

        return $this;
    }

    /**
     * Get userAllegro
     *
     * @return \Infogold\UserBundle\Entity\User
     */
    public function getUserAllegro()
    {
        return $this->User_Allegro;
    }

    /**
     * Set loginAllegro
     *
     * @param string $loginAllegro
     *
     * @return Allegro
     */
    public function setLoginAllegro($loginAllegro)
    {
        $this->LoginAllegro = $loginAllegro;

        return $this;
    }

    /**
     * Get loginAllegro
     *
     * @return string
     */
    public function getLoginAllegro()
    {
        return $this->LoginAllegro;
    }

    /**
     * Set passwordAllegro
     *
     * @param string $passwordAllegro
     *
     * @return Allegro
     */
    public function setPasswordAllegro($passwordAllegro)
    {              
        $this->PasswordAllegro = $passwordAllegro;

        return $this;
    }

    /**
     * Get passwordAllegro
     *
     * @return string
     */
    public function getPasswordAllegro()
    {
        return $this->PasswordAllegro;
    }
    
     /**
     * Set Iv
     *
     * @param string $Iv
     *
     * @return Iv
     */
    public function setIv($Iv)
    {              
        $this->Iv = $Iv;

        return $this;
    }

    /**
     * Get Iv
     *
     * @return string
     */
    public function getIv()
    {
        return $this->Iv;
    }
}
