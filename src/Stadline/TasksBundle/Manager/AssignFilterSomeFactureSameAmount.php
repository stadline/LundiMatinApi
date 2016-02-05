<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/29/16
 * Time: 10:52 AM
 */

namespace Stadline\TasksBundle\Manager;


class AssignFilterSomeFactureSameAmount implements AssignFactureMatchInterface {

    public function assignFilter($montantfacture, $amount, $affaires)
    {
        if(!empty($montantfacture[$amount]) && count($montantfacture[$amount])> 1 )
        {
            $maj = 'pas de mise à jour';
            $alerte = 'il existe plusieurs factures avec le même montant';

            return array('alerte' => $alerte, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}