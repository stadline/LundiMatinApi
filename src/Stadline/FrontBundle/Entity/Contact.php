<?php

namespace Stadline\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Stadline\FrontBundle\Repository\ContactRepository")
 */
class Contact
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
     * @ORM\Column(name="ref", type="string", length=255)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="hashedRef", type="string", length=255)
     */
    private $hashedRef;


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
     * Set ref
     *
     * @param string $ref
     * @return Contact
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string 
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set hashedRef
     *
     * @param string $hashedRef
     * @return Contact
     */
    public function setHashedRef($hashedRef)
    {
        $this->hashedRef = $hashedRef;

        return $this;
    }

    /**
     * Get hashedRef
     *
     * @return string 
     */
    public function getHashedRef()
    {
        return $this->hashedRef;
    }
}
