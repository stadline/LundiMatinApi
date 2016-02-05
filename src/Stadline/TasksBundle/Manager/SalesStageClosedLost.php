<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/4/16
 * Time: 9:47 AM
 */

namespace Stadline\TasksBundle\Manager;


class SalesStageClosedLost implements StructurePhaseMatchInterface {

    public function salesStageFilter($value, $data, $sugarClient)
    {
        if(($value[0]['etat_doc'] == 17) && ($data->getSalesStage() != 'Closed Lost'))
        {
            $query = array( array("name" => "id", "value" => $data->getid()),
                array("name" => "sales_stage", "value" =>'Closed Lost' ));

            $sugarClient->setOpportunities($query);//on fait la mise à jour

            $maj = 'mise à jour cas 17 effectuée';
            $erreur = 'aucune erreur';

            return array('erreur' => $erreur, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}