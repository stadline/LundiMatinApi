<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/4/16
 * Time: 9:57 AM
 */

namespace Stadline\TasksBundle\Manager;


class SalesStageClosedWon implements StructurePhaseMatchInterface {

    public function salesStageFilter($value, $data, $sugarClient)
    {
        if(($value[0]['etat_doc'] == 19) && ($data->getSalesStage() != 'Closed Won'))
        {
            $query = array( array("name" => "id", "value" => $data->getid()),
                array("name" => "sales_stage", "value" =>'Closed Won'));
            $sugarClient->setOpportunities($query);//on fait la mise à jour

            $maj = 'mise à jour cas 19 effectuée';
            $erreur = 'aucune erreur';

            return array('erreur' => $erreur, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}