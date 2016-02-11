<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/29/16
 * Time: 10:59 AM
 */

namespace Stadline\TasksBundle\Manager;


class AssignFilterSomeAffairSameAmountNoFacture implements AssignFactureMatchInterface {

    public function assignFilter($montantfacture, $amount, $affaires)
    {
        if(empty($montantfacture[$amount]) && count($affaires) > 1)
        {
            $maj = 'pas de mise à jour';
            $alerte = 'il existe plusieurs affaires avec le même montant et aucune facture ne correspond';

            return array('alerte' => $alerte, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}