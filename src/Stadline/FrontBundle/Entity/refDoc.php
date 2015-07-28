<?php

namespace Stadline\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * refDoc
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Stadline\FrontBundle\Entity\refDocRepository")
 */
class refDoc
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="refDoc", type="string", length=255)
     */
    private $refDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="refDocEncrypt", type="string", length=255)
     */
    private $refDocEncrypt;


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
     * Set refDoc
     *
     * @param string $refDoc
     * @return refDoc
     */
    public function setRefDoc($refDoc)
    {
        $this->refDoc = $refDoc;

        return $this;
    }

    /**
     * Get refDoc
     *
     * @return string 
     */
    public function getRefDoc()
    {
        return $this->refDoc;
    }

    /**
     * Set refDocEncrypt
     *
     * @param string $refDocEncrypt
     * @return refDoc
     */
    public function setRefDocEncrypt($refDocEncrypt)
    {
        $this->refDocEncrypt = $refDocEncrypt;

        return $this;
    }

    /**
     * Get refDocEncrypt
     *
     * @return string 
     */
    public function getRefDocEncrypt()
    {
        return $this->refDocEncrypt;
    }
}
