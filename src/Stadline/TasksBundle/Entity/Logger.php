<?php

namespace Stadline\TasksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logger
 *
 * @ORM\Table()
 * @ORM\Entity()
 *
 */
class Logger
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
     * @ORM\Column(name="ref_affaire", type="string", length=255)
     */
    private $refAffaire;

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
     * Set refAffaire
     *
     * @param string $refAffaire
     * @return Logger
     */
    public function setRefAffaire($refAffaire)
    {
        $this->refAffaire = $refAffaire;

        return $this;
    }

    /**
     * Get refAffaire
     *
     * @return string 
     */
    public function getRefAffaire()
    {
        return $this->refAffaire;
    }

    /**
     * Set date
     *
     * @param string $date
     * @return Logger
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
     * @return Logger
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
     * @return Logger
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
