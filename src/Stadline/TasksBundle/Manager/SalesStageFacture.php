<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/4/16
 * Time: 9:53 AM
 */

namespace Stadline\TasksBundle\Manager;


class SalesStageFacture implements StructurePhaseMatchInterface {

    public function salesStageFilter($value, $data, $sugarClient)
    {
        if(($value[0]['etat_doc'] == 18) && ($data->getSalesStage() != 'facture'))
        {
            $query = array( array("name" => "id", "value" => $data->getid()),
                array("name" => "sales_stage", "value" =>'facture' ));
            $sugarClient->setOpportunities($query);//on fait la mise à jour

            $maj = 'mise à jour cas 18 effectuée';
            $erreur = 'aucune erreur';

            return array('erreur' => $erreur, 'maj' => $maj);
        }
        else {
            return null;
        }
    }
}