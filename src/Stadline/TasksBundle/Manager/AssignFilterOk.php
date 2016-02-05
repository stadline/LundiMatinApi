<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/29/16
 * Time: 11:01 AM
 */

namespace Stadline\TasksBundle\Manager;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AssignFilterOk implements AssignFactureMatchInterface, ContainerAwareInterface {

    private $container = null;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function assignFilter($montantfacture, $amount, $affaires)
    {
        $sugarClient = $this->getSugarClient();
        $ManagerCommand = $this->getCommandManager();

        if(!empty($montantfacture[$amount]) && count($montantfacture[$amount])== 1 && count($affaires) == 1 && $affaires[0]["date_closed"]->diff($montantfacture[$amount][0]["date_creation"],true)->format('%R%a days') < '+90')
        {
            $refDoc = $ManagerCommand->getpdf($montantfacture[$amount][0]["ref_doc"]);
            $pdfLink = 'http://billing.stadline.com/facture/pdf/'.$refDoc; // on met un lien pour que le client puisse telecharger sa facture

            $query = array(array("name" => "id", "value" => $affaires[0]["ref_sugar_id"]),
                array("name" => "num_lmb_fact_c", "value" => $montantfacture[$amount][0]["ref_doc"]),
                array("name" => "pdf_lmb_fact_c", "value" => $pdfLink));

            $sugarClient->setOpportunities($query); // on fait la mise à jour sur sugar

            $maj = 'mise à jour effectuée';
            $alerte = 'aucune erreur';
            unset($montantfacture[$amount]);

            return array('alerte' => $alerte, 'maj' => $maj);
        }
        else {
            return null;
        }
    }

    public function getSugarClient() {
        return $this->container->get('stadline_sugar_crm_client');
    }

    public function getLundiMatinClient() {
        return $this->container->get('stadline_front.soap_service');
    }

    public function getCommandManager() {
        return $this->container->get('stadline_tasks.ManagerCommand');
    }
}