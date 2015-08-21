<?php

namespace Stadline\TasksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssignfactureLog
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AssignfactureLog
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
     * @ORM\Column(name="date", type="string", length=255)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="erreur", type="string", length=255)
     */
    private $erreur;

    /**
     * @var string
     *
     * @ORM\Column(name="maj", type="string", length=255)
     */
    private $maj;


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
     *
     * @return AssignfactureLog
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
     * Set date
     *
     * @param string $date
     *
     * @return AssignfactureLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set erreur
     *
     * @param string $erreur
     *
     * @return AssignfactureLog
     */
    public function setErreur($erreur)
    {
        $this->erreur = $erreur;

        return $this;
    }

    /**
     * Get erreur
     *
     * @return string
     */
    public function getErreur()
    {
        return $this->erreur;
    }

    /**
     * Set maj
     *
     * @param string $maj
     *
     * @return AssignfactureLog
     */
    public function setMaj($maj)
    {
        $this->maj = $maj;

        return $this;
    }

    /**
     * Get maj
     *
     * @return string
     */
    public function getMaj()
    {
        return $this->maj;
    }
}

