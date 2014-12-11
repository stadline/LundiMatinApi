<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 11/12/14
 * Time: 15:33
 */

namespace Stadline\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Contact
 * @package Stadline\FrontBundle\Entity
 * @ORM\Entity(repositoryClass="Stadline\FrontBundle\Repository\ContactRepository")
 * @ORM\Table(name="Contact")
 */
class Contact
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $hashedRef;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param $ref
     * @return $this
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashedRef()
    {
        return $this->hashedRef;
    }

    /**
     * @param $hashedRef
     * @return $this
     */
    public function setHashedRef($hashedRef)
    {
        $this->hashedRef = $hashedRef;
        return $this;
    }
} 