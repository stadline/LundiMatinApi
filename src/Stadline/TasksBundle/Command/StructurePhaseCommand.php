<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 30/07/15
 * Time: 09:28
 */

namespace Stadline\TasksBundle\Command;



use Stadline\FrontBundle\StadlineFrontBundle;
use Stadline\TasksBundle\Entity\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stadline\FrontBundle\Service\SoapService;

class StructurePhaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('Stadline:structurephase')
            ->setDescription('Mettre à jour les factures');


    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $container = $this->getContainer();
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $clientlog = $container->get('stadline_tasks.log');
        $accounts = $sugarClient->getAccounts();
        $date = date("Y-m-d H:i:s");

        foreach ($accounts as $value) {
            $data[] = $value->getid();
        }
        unset($data[5]);
        $data = array_values($data); // $data contient les numeros de clients
        // on enleve le 7 77  qui ne sert à rien




        $testdata[] = '7aa3a0b2-ebb8-108e-1bc6-55c895a8e326';

        foreach ($testdata as $value) // on cherche à  obtenir les phase de vente pour chaque affaires
        {

            $opportunities = $sugarClient->getOpportunities($value);// on recupere toutes les affaires

            if (count($opportunities) > 0) {
                foreach ($opportunities as $SalesStage) {

                    if ($SalesStage->getSalesStage() != 'Closed Won' and $SalesStage->getSalesStage() != 'Closed Lost') {
                        $affaires[] = $SalesStage; // correspond au affaire non payé et non perdu
                    }
                }
            }
        }
        if(!isset($affaires[0])){die();}




        foreach ($affaires as $value) {
            if ($value->getnumfact() != '') {
                $affaire[] = $value;
            }
        }
        if(!isset($affaire[0])){die();}

        // on recupere les factures de lundi matin correspondant aux numeros de factures des affaires dans sugar

        $client = $container->get('stadline_front.soap_service');
        foreach ($affaire as $key => $value) {
            $ref = $value->getnumfact();

            $factures[] = $client->getFacturesByRef($ref);


        }


        //unset($factures[0]);
        //$factures = array_values($factures);


        foreach ($factures as $index => $value) {

            $factures[$index]['details'] = $client->getDocument($value[0]['ref_doc']); //pour obtenir le montant de la facture

        }


        //unset($affaire[0]);
        //$affaire = array_values($affaire);



        foreach ($affaire as $index =>$data) {



                if ($factures[$index]['details']['montant_ttc'] != $data->getamount()) // comparait les montants des affaires et des factures
                {
                    $erreur = 'erreur montant';
                    $maj = 'pas de mise à jour';
                    $clientlog->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);

                }
                else {


                    if ($factures[$index][0]['etat_doc'] == 17 and $data->getSalesStage() != 'Closed Lost') {

                        $query = array("id" => $data->getid(),
                            "update" => 'Closed Lost', // on veut changer la phase de vente
                             "name" => "sales_stage"
                        );




                        $test = $sugarClient->setOpportunities($query);

                        $maj = 'mise à jour effectuée';
                        $erreur = 'aucune erreur';
                        $clientlog->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);
                    }

                }



        }
        die();

    }

}