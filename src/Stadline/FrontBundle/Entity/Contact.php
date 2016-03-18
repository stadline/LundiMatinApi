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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="hashedRef", type="string", length=255)
     */
    private $hashedRef;

    /**
     * @var string
     *
     * @ORM\Column(name="sugar_account_id", type="string", nullable=true)
     */
    private $sugarAccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="zendesk_organization_id", type="integer", nullable=true)
     */
    private $zendeskOrganizationId;


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
     * Set name
     *
     * @param string $name
     * @return Contact
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

    /**
     * Set SugarCRM Account ID
     *
     * @param string $sugarAccountId
     *
     * @return Contact
     */
    public function setSugarAccountId($sugarAccountId)
    {
        $this->sugarAccountId = $sugarAccountId;

        return $this;
    }

    /**
     * Get sugarCRM Account ID
     *
     * @return string
     */
    public function getSugarAccountId()
    {
        return $this->sugarAccountId;
    }

    /**
     * Set Zendesk Organization ID
     *
     * @param integer $zendeskId
     *
     * @return Contact
     */
    public function setZendeskOrganizationId($zendeskId)
    {
        $this->zendeskOrganizationId = $zendeskId;

        return $this;
    }

    /**
     * Get Zendesk Organization ID
     *
     * @return integer
     */
    public function getZendeskOrganizationId()
    {
        return $this->zendeskOrganizationId;
    }
}
