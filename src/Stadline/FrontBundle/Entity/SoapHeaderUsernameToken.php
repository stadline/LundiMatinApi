<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 11/12/14
 * Time: 11:05
 */

namespace Stadline\FrontBundle\Entity;


class SoapHeaderUsernameToken
{
    public $Password;
    public $Username;

    public function __construct($l, $p)
    {
        $this->Password = $p;
        $this->Username = $l;
    }
} 