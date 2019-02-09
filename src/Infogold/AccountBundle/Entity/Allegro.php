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
    protected $UserAllegro;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $AllegroClientID;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $AllegroClientSecret;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $Token;

    /**
     * @var \DateTime $DateTimeToken
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="Token", value="true")
     */
    protected $DateTimeToken;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $RefreshToken;

    /**
     * @var \DateTime $DateTimeRefreshToken
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="RefreshToken", value="true")
     */
    protected $DateTimeRefreshToken;

    
    public function getAllegroClientSecret() {
        return $this->AllegroClientSecret;
    }

    public function setAllegroClientSecret($AllegroClientSecret) {
        $this->AllegroClientSecret = $AllegroClientSecret;
        return $this;
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
     * Set allegroClientID
     *
     * @param string $allegroClientID
     *
     * @return Allegro
     */
    public function setAllegroClientID($allegroClientID) {
        $this->AllegroClientID = $allegroClientID;

        return $this;
    }

    /**
     * Get allegroClientID
     *
     * @return string
     */
    public function getAllegroClientID() {
        return $this->AllegroClientID;
    }

    /**
     * Set userAllegro
     *
     * @param \Infogold\UserBundle\Entity\User $userAllegro
     *
     * @return Allegro
     */
    public function setUserAllegro(\Infogold\UserBundle\Entity\User $userAllegro = null) {
        $this->UserAllegro = $userAllegro;

        return $this;
    }

    /**
     * Get userAllegro
     *
     * @return \Infogold\UserBundle\Entity\User
     */
    public function getUserAllegro() {
        return $this->UserAllegro;
    }
    function getToken() {
        return $this->Token;
    }

    function getDateTimeToken() {
        return $this->DateTimeToken;
    }

    function getRefreshToken() {
        return $this->RefreshToken;
    }

    function getDateTimeRefreshToken() {
        return $this->DateTimeRefreshToken;
    }

    function setToken($Token) {
        $this->Token = $Token;
        return $this;
    }

    function setDateTimeToken(\DateTime $DateTimeToken) {
        $this->DateTimeToken = $DateTimeToken;
        return $this;
    }

    function setRefreshToken($RefreshToken) {
        $this->RefreshToken = $RefreshToken;
        return $this;
    }

    function setDateTimeRefreshToken(\DateTime $DateTimeRefreshToken) {
        $this->DateTimeRefreshToken = $DateTimeRefreshToken;
        return $this;
    }


}
