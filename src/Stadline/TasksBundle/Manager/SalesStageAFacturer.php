<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/4/16
 * Time: 9:43 AM
 */

namespace Stadline\TasksBundle\Manager;


class SalesStageAFacturer implements StructurePhaseMatchInterface {

    public function salesStageFilter($value, $data, $sugarClient)
    {
        if(($value[0]['etat_doc'] == 16) && ($data->getSalesStage() != 'a_facturer'))
        {
            $query = array( array("name" => "id", "value" => $data->getid()),
                array("name" => "sales_stage", "value" =>'a_facturer' ));

            $sugarClient->setOpportunities($query);//on fait la mise à jour

            $maj = 'mise à jour cas 16 effectuée';
            $erreur = 'aucune erreur';

            return array('erreur' => $erreur, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}