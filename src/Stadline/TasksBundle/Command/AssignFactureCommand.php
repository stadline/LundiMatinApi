<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 03/08/15
 * Time: 14:52
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

class AssignFactureCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this
            ->setName('Stadline:assignfacture')
            ->setDescription('Assigner les numeros de factures de LundiMatin aux affaires de Sugar');

    }




    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");
        $clientlog = $container->get('stadline_tasks.log');
        $date = date("Y-m-d H:i:s");


        // cette tache permet d'attribuer un numero de facture à toute les affaires
        foreach ($accounts as $value) {
            $numclients[] = $value->getid();//on recupere tout les id des comptes clients de Sugar
        }



        $valeurtest[] = '7aa3a0b2-ebb8-108e-1bc6-55c895a8e326';

        // Pour chaque compte de Sugar on récupere toutes ses affaires et on ne selectionne que celles qui ne sont pas perdue ou payée
        foreach ($valeurtest as $value)
        {

            $opportunities = $sugarClient->getOpportunities($value);



            if (count($opportunities) > 0) {
                foreach ($opportunities as $SalesStage) {

                    if ( $SalesStage->getSalesStage() != 'Closed Lost' and $SalesStage->getSalesStage() != 'Closed Won') {
                        $affaires[] = $SalesStage;
                    }

                }
            }

        }

        //on selectionne les affaires qui n'ont pas encore de numero de facture
        $affaire_sansnum = [];
        foreach($affaires as $value)
        {
            if( $value->getnumfact() == '')
            {
                $affaire_sansnum[] =  $value;
            }
        }

        if(empty($affaire_sansnum[0])) {var_dump('aucune mise à jour nécessaire');die();}



        // on recupere les numeros de client Lundimatin des affaires
        foreach ($affaire_sansnum as $value) {

            if($value->getidLMB() != ""){
                $idLMB[] = $value->getidLMB();}

            $id[] = $value->getid();
        }
        $idLMB = array_unique($idLMB);// on fait en sorte d'enlever les doublons
        $idLMB = array_values($idLMB);
        $client = $container->get('stadline_front.soap_service');


        foreach($affaires as $data)
        {
            $num[] = $data->getnumfact();

        }

        foreach ($idLMB as $value)
        {

            $ALLfactures = $client->getFacturesByRefClient($value); // on prend les factures pour chaque ref obtenue juste avant



            foreach($ALLfactures as $data)
            {

                if(!in_array($data['ref_doc'],$num))// si une facture n'est pas reliée à une affaire on la prend , il faudra l'associer plus tard
                {
                    $fact[] = $data;
                }
            }
        }




        // on prend le detail des factures pour avoir le montant
        foreach ($fact as $index=>$value) {
            $fact[$index]['details'] = $client->getDocument($value['ref_doc']);


        }



        foreach($affaire_sansnum as $value) {
            $final = [];
            foreach ($fact as $data) {

                // on associe les factures et affaires sans numero de facture en comparant leur montant , si ils sont pareils on selectionne la facture
                if ($value->getamount() == $data['details']['net_ht'] and $value->getidLMB() == $data['ref_contact']) {
                    $final[] = $data;
                }
            }




            // pour faire la mise à jour il faut qu'une et une seule facture soit associée à une affaire si c'est le cas on fait la mise à jour
            if (!empty($final[0]) and empty($final[1])) {

                $refDoc = $clientlog->getpdf($final[0]['ref_doc']);


                $pdf = 'http://billing.stadline.com/facture/pdf/'; // on met un lien pour que le client puisse telecharger sa facture
                $pdf .= $refDoc[0];

                $query = array(array("name" => "id", "value" => $value->getid()),
                    array("name" => "num_lmb_fact_c", "value" => $final[0]['ref_doc']),
                    array("name" => "pdf_lmb_fact_c", "value" => $pdf));


                $test = $sugarClient->setOpportunities($query); // on fait la mise à jour
                $maj = 'mise à jour effectuée';
                $alerte = 'aucune erreur';


            }
            // il y a plusieurs factures associées à une affaire , il y a une erreur
            elseif (!empty($final[1])) {
                $alerte = 'il y a plusieurs factures ayant le même montant que cette affaire';
                $maj = 'pas de mise à jour';
            }
            // il n'y a pas de facture associée à cette affaire , il y a une erreur
            else {
                $alerte = 'aucun montant ne correspond à cette affaire';
                $maj = 'pas de mise à jour';
            }


            $clientlog->getValueAssign($value->getname(), $date, $alerte, $maj); // on envoit les logs

        }
        die();

    }

}