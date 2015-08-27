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
            ->setDescription('Mettre à jour les phases de ventes des affaires sur Sugar');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $clientlog = $container->get('stadline_tasks.log');
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");
        $date = date("Y-m-d H:i:s");

        //cette tache cron va récuperer les affaires sur Sugar et trouver la factures associée pour comparer les phases de ventes et les mettres à jour
        foreach ($accounts as $value) {
            $data[] = $value->getid();  //on recupere tout les id des comptes clients de Sugar
        }

        $testdata[] = '7aa3a0b2-ebb8-108e-1bc6-55c895a8e326';


        // Pour chaque compte de Sugar on récupere toutes ses affaires et on ne selectionne que celles qui ne sont pas perdue ou payée
        foreach ($data as $value) //
        {

            $opportunities = $sugarClient->getOpportunities($value);

            if (count($opportunities) > 0) {
                foreach ($opportunities as $SalesStage) {

                    if ($SalesStage->getSalesStage() != 'Closed Won' and $SalesStage->getSalesStage() != 'Closed Lost') {
                        $affaires[] = $SalesStage;
                    }
                }
            }
        }

        if(!isset($affaires[0])){die();} // si aucune facture n'est trouvée le tache s'arrete

        // on veut que nos affaires est un numero de facture pour pouvoir les relier à LundiMatin
        foreach ($affaires as $value) {
            if ($value->getnumfact() != '') {
                $affaire[] = $value;
            }
        }

        if(!isset($affaire[0])){die();}

        // on extrait les factures de LundiMatin avec les reference de factures obtenu juste avant
        $client = $container->get('stadline_front.soap_service');
        foreach ($affaire as $key => $value) {
            $ref = $value->getnumfact();
            $factures[] = $client->getFacturesByRef($ref);
        }

        // on veut le detail des factures pour avoir le montant
        foreach ($factures as $index => $value) {
            $factures[$index]['details'] = $client->getDocument($value[0]['ref_doc']); //pour obtenir le montant de la facture
        }

        // on compare le montant des affaires et des factures pour voir si il n'y a pas d'erreur
        foreach ($affaire as $index =>$data) {

                // si il y a une erreur on envoie des logs d'erreur et il n'y à pas de mise à jour
                if ($factures[$index]['details']['net_ht'] != $data->getamount()) // comparait les montants des affaires et des factures
                {
                    $erreur = 'erreur montant';
                    $maj = 'pas de mise à jour';
                    $clientlog->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);// on envoie les logs
                }
                else {
                    // si il n'y a pas d'erreur on regarde si la mise à jour est nécessaire puis on l'effectue et on envoie des logs
                    if ($factures[$index][0]['etat_doc'] == 17 && $data->getSalesStage() != 'Closed Lost') {

                        $query = array( array("name" => "id", "value" => $data->getid()),
                                        array("name" => "sales_stage", "value" =>'Closed Lost' ));

                        $test = $sugarClient->setOpportunities($query);//on fait la mise à jour

                        $maj = 'mise à jour effectuée';
                        $erreur = 'aucune erreur';
                        $clientlog->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);// on envoie les logs
                    }
                    elseif ($factures[$index][0]['etat_doc'] == 19 and $data->getSalesStage() != 'Closed Won')
                    {

                        $query = array( array("name" => "id", "value" => $data->getid()),
                                        array("name" => "sales_stage", "value" =>'Closed Won' ));
                        $test = $sugarClient->setOpportunities($query);//on fait la mise à jour

                        $maj = 'mise à jour effectuée';
                        $erreur = 'aucune erreur';
                        $clientlog->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);// on envoie les logs
                    }

                    elseif ($factures[$index][0]['etat_doc'] == 18 and $data->getSalesStage() != 'signe')
                    {

                        $query = array( array("name" => "id", "value" => $data->getid()),
                                        array("name" => "sales_stage", "value" =>'signe'));
                        $test = $sugarClient->setOpportunities($query);//on fait la mise à jour

                        $maj = 'mise à jour effectuée';
                        $erreur = 'aucune erreur';
                        $clientlog->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);// on envoie les logs
                    }

                }
        }
    }

}