<?php
namespace Infogold\KonsultantBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Infogold\KonsultantBundle\Entity\CzasPracy
 * @ORM\Entity
 * @ORM\Table(name="czaspracy")
 */
class CzasPracy {
        /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var datetime $zalogowanie
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $zalogowanie;
    
     /**
     * @var datetime $wylogowanie
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $wylogowanie;
    
       /**
     * @ORM\ManyToOne(targetEntity="Infogold\KonsultantBundle\Entity\Konsultant", inversedBy="KonsultantaCzasPracy")
     * @ORM\JoinColumn(name="KonsultantaCzasy_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $KonsultantaCzasy;

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
     * Set zalogowanie
     *
     * @param \DateTime $zalogowanie
     * @return CzasPracy
     */
    public function setZalogowanie($zalogowanie)
    {
        $this->zalogowanie = $zalogowanie;

        return $this;
    }

    /**
     * Get zalogowanie
     *
     * @return \DateTime 
     */
    public function getZalogowanie()
    {
        return $this->zalogowanie;
    }

    /**
     * Set wylogowanie
     *
     * @param \DateTime $wylogowanie
     * @return CzasPracy
     */
    public function setWylogowanie($wylogowanie)
    {
        $this->wylogowanie = $wylogowanie;

        return $this;
    }

    /**
     * Get wylogowanie
     *
     * @return \DateTime 
     */
    public function getWylogowanie()
    {
        return $this->wylogowanie;
    }

    /**
     * Set KonsultantaCzasy
     *
     * @param \Infogold\KonsultantBundle\Entity\Konsultant $konsultantaCzasy
     * @return CzasPracy
     */
    public function setKonsultantaCzasy(\Infogold\KonsultantBundle\Entity\Konsultant $konsultantaCzasy = null)
    {
        $this->KonsultantaCzasy = $konsultantaCzasy;

        return $this;
    }

    /**
     * Get KonsultantaCzasy
     *
     * @return \Infogold\KonsultantBundle\Entity\Konsultant 
     */
    public function getKonsultantaCzasy()
    {
        return $this->KonsultantaCzasy;
    }
}
