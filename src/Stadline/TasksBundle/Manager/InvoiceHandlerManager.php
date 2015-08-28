<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 03/08/15
 * Time: 14:52
 */


namespace Stadline\TasksBundle\Manager;

use Doctrine\ORM\Mapping as ORM;

class InvoiceHandlerManager
{
    const SALE_STAGE_LOST = 'Closed Lost';
    const SALE_STAGE_WON = 'Closed Won';
    const DATE_INTERVALLE = '+90';

    public function executeAssignFactureCommand($container, $input, $output)
    {
        // get sugar client
        /** @var  Client */
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");
        // get logs service
        $date = date("Y-m-d H:i:s");
        $ManagerCommand = $container->get('stadline_tasks.ManagerCommand');


        $numclients = array();
        // pour chaque compte client, je récupère l'id du compte pour ensuite aller chercher les affaires.
        foreach ($accounts as $value) {
            $numclients[$value->getid()] = $value->getname();//on recupere tout les id des comptes clients de Sugar

            if($value->getname() == "Mobile Angelo" or $value->getname() == "7 à 77 fitness" or $value->getname() == "Kipsta" ) {
                $valeurtest[$value->getid()] = $value->getname();
            }
        }



        $affaires = array();
        $affaire_sansnum = array();
        $idLMB = array();
        // Pour chaque compte de Sugar on récupere toutes ses affaires et on ne selectionne que celles qui ne sont pas perdue ou payée
        foreach ($valeurtest as $clientId => $clientName)
        {
            $opportunities = $sugarClient->getOpportunities($clientId);
            if (count($opportunities) > 0) {
                foreach ($opportunities as $SalesStage) {
                    if ( $SalesStage->getSalesStage() != self::SALE_STAGE_LOST and $SalesStage->getSalesStage() != self::SALE_STAGE_WON) {
                        $affaires[] = $SalesStage;
                        if( $SalesStage->getnumfact() == '')
                        {
                            //on selectionne les affaires qui n'ont pas encore de numero de facture
                            $affaire_sansnum[] =  $SalesStage;

                            // on recupere les numeros de client Lundimatin des affaires
                            if ($SalesStage->getidLMB() != "") {
                                $idLMB[] = $SalesStage->getidLMB();
                            }
                            $output->writeln('> Affaire sans numéro LM trouvée pour '.$clientName.' : '.$SalesStage->getname());
                        } else {
                            $output->writeln('> Affaire avec numéro LM trouvée pour '.$clientName.' : '.$SalesStage->getname());
                        }
                    }
                }
            }
        }

        $idLMB = array_values(array_unique($idLMB));// on fait en sorte d'enlever les doublons et on supprime les index.

        // si j'ai aucune affaire sans num de facture, je peux arréter le script
        if(empty($affaire_sansnum[0])) {
            $output->writeln('> aucune mise à jour nécessaire');
            exit;
        }

        // client pour lundiMatin
        $num = array();
        foreach($affaires as $data)
        {
            $num[] = $data->getnumfact();
        }

        $client = $container->get('stadline_front.soap_service');
        foreach ($idLMB as $value)
        {
            // on prend les factures du compte qui a au moins une facture sans numéro sur sugar
            $ALLfactures = $client->getFacturesByRefClient($value);
            foreach($ALLfactures as $data)
            {
                // si une facture n'est pas reliée à une affaire on la prend , il faudra l'associer plus tard
                if(!in_array($data['ref_doc'],$num))
                {
                    // je vais chercher plus d'info sur cette facture pour avoir le montant
                    $data['details'] =  $client->getDocument($data['ref_doc']);
                    $fact[] = $data;
                    $output->writeln('> Facture inconnue sur Sugar trouvée : '.$data['ref_doc']);
                }
            }
        }



        foreach($affaire_sansnum as $value) {

            $montantaffaire[$value->getamount()][] = array($value->getname(),$value->getid(),$value->getClosedAt());
        }
        foreach ($fact as $data) {


            $ht = $data['details']['net_ht'];
            $dateclosed = new \DateTime($data['date_creation_doc']);

            $montantfacture[$ht][] = array($data['ref_doc'],$dateclosed);
        }






                // on associe les factures et affaires sans numero de facture en comparant leur montant , si ils sont pareils on selectionne la facture
        foreach($montantaffaire as $index => $value)
        {

            if(!empty($montantfacture[$index]) && count($montantfacture[$index])== 1 && count($value) == 1 && $value[0][2]->diff($montantfacture[$index][0][1],true)->format('%R%a days') < self::DATE_INTERVALLE)
            {

                $refDoc = $ManagerCommand->getpdf($montantfacture[$index][0][0]);
                $pdf = 'http://billing.stadline.com/facture/pdf/'.$refDoc[0]; // on met un lien pour que le client puisse telecharger sa facture

                $query = array(array("name" => "id", "value" => $value[0][1]),
                    array("name" => "num_lmb_fact_c", "value" => $montantfacture[$index][0][0]),
                    array("name" => "pdf_lmb_fact_c", "value" => $pdf));

                //$sugarClient->setOpportunities($query); // on fait la mise à jour sur sugar
                $maj = 'mise à jour effectuée';
                $alerte = 'aucune erreur';
                $output->writeln('> '.$value[0][0].' : '.$alerte.' - '.$maj);
            }

            elseif(!empty($montantfacture[$index]) && count($montantfacture[$index])> 1 or !empty($montantfacture[$index]) && count($value) > 1)
            {
                $maj = 'pas de mise à jour';
                $alerte = 'il existe plusieurs factures avec le même montant';
                $output->writeln('> '.$value[0][0].' : '.$alerte.' - '.$maj);
            }

            else
            {
                $alerte = 'aucun montant ne correspond à cette affaire';
                $maj = 'pas de mise à jour';
                $output->writeln('> '.$value[0][0].' : '.$alerte.' - '.$maj);
            }

            //$ManagerCommand->getValueAssign($value[0][0], $date, $alerte, $maj); // on envoit les logs
        }

    }





    public function executeStructurePhaseCommand($container, $input, $output)
    {
        // get sugar client
        /** @var  Client */
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");
        $date = date("Y-m-d H:i:s");
        $ManagerCommand = $container->get('stadline_tasks.ManagerCommand');


        $numclients = array();
        $valeurtest = array();
        // pour chaque compte client, je récupère l'id du compte pour ensuite aller chercher les affaires.
        foreach ($accounts as $value) {
            $numclients[$value->getid()] = $value->getname();//on recupere tout les id des comptes clients de Sugar

            if($value->getname() == "Test Lundi Matin") {
                $valeurtest[$value->getid()] = $value->getname();
            }
        }

        $affaires = array();
        $factures = array();
        $client = $container->get('stadline_front.soap_service');
        // Pour chaque compte de Sugar on récupere toutes ses affaires et on ne selectionne que celles qui ne sont pas perdue ou payée
        foreach ($valeurtest as $clientId => $clientName) //
        {

            $opportunities = $sugarClient->getOpportunities($clientId);

            if (count($opportunities) > 0) {
                foreach ($opportunities as $SalesStage) {

                    if ( $SalesStage->getSalesStage() != self::SALE_STAGE_LOST && $SalesStage->getSalesStage() != self::SALE_STAGE_WON && $SalesStage->getnumfact() != '') {
                        $output->writeln('> Affaire avec numéro LM trouvée pour '.$clientName.' : '.$SalesStage->getname());
                        $affaires[] = $SalesStage;
                        $ref = $SalesStage->getnumfact();
                        $factures[] = $client->getFacturesByRef($ref);
                    }
                    else {
                        $output->writeln('> Affaire sans numéro LM trouvée pour ' . $clientName . ' : ' . $SalesStage->getname());
                    }
                }
            }
        }

        // on veut le detail des factures pour avoir le montant
        foreach ($factures as $index => $value) {
            $factures[$index]['details'] = $client->getDocument($value[0]['ref_doc']); //pour obtenir le montant de la facture
        }



        // on compare le montant des affaires et des factures pour voir si il n'y a pas d'erreur
        foreach ($affaires as $index =>$data) {

            foreach($factures as $value)
            {
                var_dump($value['details']['net_ht']);
                var_dump($data->getamount());
                if( $value['details']['net_ht'] == $data->getamount() &&  $data->getClosedAt()->diff($value[0]['date_creation_doc'],true)->format('%R%a days') < self::DATE_INTERVALLE) {


                    switch($value[0]['etat_doc'])
                    {
                        case 17:
                            if($data->getSalesStage() != 'Closed Lost')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'Closed Lost' ));

                                //$sugarClient->setOpportunities($query);//on fait la mise à jour

                                $maj = 'mise à jour effectuée';
                                $erreur = 'aucune erreur';
                            }
                        case 18:
                            if($data->getSalesStage() != 'Closed Won')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'Closed Won' ));
                                //$sugarClient->setOpportunities($query);//on fait la mise à jour

                                $maj = 'mise à jour effectuée';
                                $erreur = 'aucune erreur';
                            }
                        case 19:
                            if($data->getSalesStage() != 'signe')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'signe'));
                                //$sugarClient->setOpportunities($query);//on fait la mise à jour

                                $maj = 'mise à jour effectuée';
                                $erreur = 'aucune erreur';
                            }
                    }

                }
                else  // comparait les montants des affaires et des factures
                {
                    $erreur = 'erreur montant';
                    $maj = 'pas de mise à jour';


                }

            }
            $output->writeln('> '.$data->getname().' : '.$erreur.' - '.$maj);
            $ManagerCommand->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);// on envoie les logs

        }
    }


}
