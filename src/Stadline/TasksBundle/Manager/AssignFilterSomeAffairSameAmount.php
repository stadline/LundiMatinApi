<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/29/16
 * Time: 10:58 AM
 */

namespace Stadline\TasksBundle\Manager;


class AssignFilterSomeAffairSameAmount implements AssignFactureMatchInterface{

    public function assignFilter($montantfacture, $amount, $affaires)
    {
        if( !empty($montantfacture[$amount]) && count($affaires) > 1)
        {
            $maj = 'pas de mise à jour';
            $alerte = 'il existe plusieurs affaires avec le même montant';

            return array('alerte' => $alerte, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}