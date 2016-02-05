<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/29/16
 * Time: 10:46 AM
 */

namespace Stadline\TasksBundle\Manager;


interface AssignFactureMatchInterface {
    public function assignFilter($montantfacture, $amount, $affaires);
}