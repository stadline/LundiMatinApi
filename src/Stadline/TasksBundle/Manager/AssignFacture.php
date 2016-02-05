<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/29/16
 * Time: 2:41 PM
 */

namespace Stadline\TasksBundle\Manager;


class AssignFacture {

    protected $assignFacture;

    public function __construct(AssignFactureMatchInterface $assignFacture){
        $this->assignFacture = $assignFacture;
    }

    public function executeAssignFactureMatch($montantfacture, $amount, $affaires) {
        return $this->assignFacture->assignFilter($montantfacture, $amount, $affaires);
    }
} 