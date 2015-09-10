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

    private $container = null;
    private $output = null;

    public function executeAssignFactureCommand($container, $input, $output)
    {
        $this->container = $container;
        $this->output = $output;

        // get sugar client
        /** @var  Client */
        $sugarClient = $this->getSugarClient();
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");

        // get logs service
        $date = date("Y-m-d H:i:s");
        $ManagerCommand = $this->getCommandManager();

        $numclients = $this->getSugarClientNumber();

        // test Kipsta
        $numclients = array('1f030ea0-1523-fbcd-d979-542c1cfc8364' => 'Kipsta');

        $affaires = $this->extractAffairesFromSugar($numclients);

        // si j'ai aucune affaire sans num de facture, je peux arréter le script
        if(empty($affaires['without_num'][0])) {
            $this->output->writeln('> aucune mise à jour nécessaire');
            exit;
        }

        // client pour lundiMatin
        $client = $container->get('stadline_front.soap_service');
        $montantfacture = array();
        foreach ($affaires['idLMB'] as $value)
        {
            // on prend les factures du compte qui a au moins une facture sans numéro sur sugar
            $allLMBInvoices = $client->getFacturesByRefClient($value);
            foreach($allLMBInvoices as $data)
            {
                // si une facture n'est pas reliée à une affaire on la prend , il faudra l'associer plus tard
                if(!in_array($data['ref_doc'], $affaires['num']) && $data['type_doc'] == '4')
                {
                    // je vais chercher plus d'info sur cette facture pour avoir le montant
                    $data['details'] =  $client->getDocument($data['ref_doc']);

                    // j'index un tableau par montant HT
                    $ht = ($data['details']['montant_ttc'] - $data['details']['montant_tva']);
                    if(!isset($montantfacture[$ht])) {
                        $montantfacture[$ht] = array();
                    }
                    // on met la date en Datetime pour faire la diffrence des deux ensuite
                    $montantfacture[$ht][] = array("ref_doc" => $data['ref_doc'], "date_creation" => new \DateTime($data['date_creation_doc']));

                    $this->output->writeln('> Facture inconnue sur Sugar trouvée : '.$data['ref_doc'].' pour le montant HT de '.$ht);
                }
            }
        }

        $affaireSortedByAmount = array();
        foreach($affaires['without_num'] as $value) {
            // init array
            if(!isset($affaireSortedByAmount[$value->getamount()])) {
                $affaireSortedByAmount[$value->getamount()] = array();
            }
            $affaireSortedByAmount[$value->getamount()][] = array("name" => $value->getname(), "ref_sugar_id" => $value->getid(), "date_closed" => $value->getClosedAt());
        }

        // on associe les factures et affaires sans numero de facture en comparant leur montant , si ils sont pareils on selectionne la facture
        foreach($affaireSortedByAmount as $amount => $affaires)
        {

            // on regarde si la facture est unique et si elle existe puis on compare la date
            if(!empty($montantfacture[$amount]) && count($montantfacture[$amount])== 1 && count($affaires) == 1 && $affaires[0]["date_closed"]->diff($montantfacture[$amount][0]["date_creation"],true)->format('%R%a days') < self::DATE_INTERVALLE)
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
            }

            elseif(!empty($montantfacture[$amount]) && count($montantfacture[$amount])> 1 )
            {
                $maj = 'pas de mise à jour';
                $alerte = 'il existe plusieurs factures avec le même montant';
            }

            elseif( !empty($montantfacture[$amount]) && count($affaires) > 1)
            {
                $maj = 'pas de mise à jour';
                $alerte = 'il existe plusieurs affaires avec le même montant';
            }

            elseif(empty($montantfacture[$amount]) && count($affaires) > 1)
            {
                $maj = 'pas de mise à jour';
                $alerte = 'il existe plusieurs affaires avec le même montant et aucune facture ne correspond';
            }
            else
            {
                $alerte = 'aucun montant ne correspond à cette affaire';
                $maj = 'pas de mise à jour';
            }

            $this->output->writeln('> '.$affaires[0]["name"].' : '.$alerte.' - '.$maj);
            $ManagerCommand->createNewLogEntry($affaires[0]["name"], $date, $alerte, $maj); // on envoit les logs
        }
    }

    public function executeStructurePhaseCommand($container, $input, $output)
    {
        $this->container = $container;
        $this->input = $input;
        $this->output = $output;

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
                foreach ($opportunities as $salesStage) {

                    if ( $salesStage->getSalesStage() != self::SALE_STAGE_LOST && $salesStage->getSalesStage() != self::SALE_STAGE_WON && $salesStage->getnumfact() != '') {
                        $this->output->writeln('> Affaire avec numéro LM trouvée pour '.$clientName.' : '.$salesStage->getname());
                        $affaires[] = $salesStage;
                        $ref = $salesStage->getnumfact();
                        $factures[] = $client->getFacturesByRef($ref);
                    }
                    else {
                        $this->output->writeln('> Affaire sans numéro LM trouvée pour ' . $clientName . ' : ' . $salesStage->getname());
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
                if( $value['details']['net_ht'] == $data->getamount() &&  $data->getClosedAt()->diff(new \DateTime($value[0]['date_creation_doc']),true)->format('%R%a days') < self::DATE_INTERVALLE) {
                    switch($value[0]['etat_doc'])
                    {
                        case 17:
                            if($data->getSalesStage() != 'Closed Lost')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'Closed Lost' ));

                                $sugarClient->setOpportunities($query);//on fait la mise à jour

                                $maj = 'mise à jour effectuée';
                                $erreur = 'aucune erreur';
                            }
                        case 18:
                            if($data->getSalesStage() != 'facture')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'facture' ));
                                $sugarClient->setOpportunities($query);//on fait la mise à jour

                                $maj = 'mise à jour effectuée';
                                $erreur = 'aucune erreur';
                            }
                        case 19:
                            if($data->getSalesStage() != 'Closed Won')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'Closed Won'));
                                $sugarClient->setOpportunities($query);//on fait la mise à jour

                                $maj = 'mise à jour effectuée';
                                $erreur = 'aucune erreur';
                            }
                        case 16:
                            if($data->getSalesStage() != 'a_facturer')
                            {
                                $query = array( array("name" => "id", "value" => $data->getid()),
                                    array("name" => "sales_stage", "value" =>'a_facturer' ));

                                $sugarClient->setOpportunities($query);//on fait la mise à jour

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
                $this->output->writeln('> '.$data->getname().' : '.$erreur.' - '.$maj);
                $ManagerCommand->getValue($factures[$index][0]['ref_doc'],$date,$erreur,$maj);// on envoie les logs

            }


        }
    }

    /**
     * Return an array of the Sugar Client Number
     * @return array collection indexed by [sugarClientId] => ClientName
     */
    private function getSugarClientNumber() {
        // get sugar client
        $sugarClient = $this->getSugarClient();
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");

        $clients = array();
        // pour chaque compte client, je récupère l'id du compte pour ensuite aller chercher les affaires.
        foreach ($accounts as $value) {
            $clients[$value->getid()] = $value->getname(); //on recupere tout les id des comptes clients de Sugar

            // update only for Lundi Matin right now
            if($value->getname() == 'Test Lundi Matin')
            {
//                $this->updateBillingLinkOnSugar($value->getid(), $value->getidLMB());
            }
        }

        return $clients;
    }

    /**
     * Update client link on sugar for a sugarClientId and LMBClientId
     * @param $sugarClientId
     * @param $LMBClientId
     */
    private function updateBillingLinkOnSugar($sugarClientId, $LMBClientId) {
        $commandManager = $this->getCommandManager();

        $LMBrefclient = $commandManager->getclient($LMBClientId);
        $clientLink = 'http://billing.stadline.com/factures/'.$LMBrefclient[0]; // on met un lien pour acceder à lundiMatin à partir de Sugar
        $query = array(array("name" =>"id","value" =>$sugarClientId),
            array("name"=>"url_etat_client_lmb_c","value" =>$clientLink));

        $this->getSugarClient()->setAccounts($query); // on fait la mise à jour sur sugar
    }

    public function extractAffairesFromSugar($numclients) {
        $sugarClient = $this->getSugarClient();

        $affaires = array();
        $affaires['all'] = array();
        $affaires['without_num'] = array();
        $affaires['num'] = array();
        $affaires['idLMB'] = array();
        // Pour chaque compte de Sugar on récupere toutes ses affaires et on ne selectionne que celles qui ne sont pas perdue ou payée
        foreach ($numclients as $clientId => $clientName)
        {
            $opportunities = $sugarClient->getOpportunities($clientId);

            if (count($opportunities) > 0) {
                foreach ($opportunities as $salesStage) {
                    if ( $salesStage->getSalesStage() != self::SALE_STAGE_LOST && $salesStage->getSalesStage() != self::SALE_STAGE_WON) {
                        $affaires['all'][] = $salesStage;
                        $affaires['num'][] = $salesStage->getnumfact();

                        if( $salesStage->getnumfact() == '')
                        {
                            //on selectionne les affaires qui n'ont pas encore de numero de facture
                            $affaires['without_num'][] =  $salesStage;

                            // on recupere les numeros de client Lundimatin du compte qui possède l'affaire si il existe.
                            if ($salesStage->getidLMB() != "") {
                                $affaires['idLMB'][] = $salesStage->getidLMB();
                            }
                            $this->output->writeln('> Affaire sans numéro LM trouvée pour '.$clientName.' : '.$salesStage->getname());
                        } else {
                            $this->output->writeln('> Affaire avec numéro LM trouvée pour '.$clientName.' : '.$salesStage->getname());
                        }
                    }
                }
            }
        }

        $affaires['idLMB'] = array_values(array_unique($affaires['idLMB']));// on fait en sorte d'enlever les doublons et on supprime les index.

        return $affaires;
    }

    private function getSugarClient() {
        return $this->container->get('stadline_sugar_crm_client');
    }

    private function getCommandManager() {
        return $this->container->get('stadline_tasks.ManagerCommand');
    }

    private function refDocEncrypt($refDoc) {
        $salt = $this->container->getParameter('secret');
        return $this->container->get('doctrine.orm.default_entity_manager')->getRepository('StadlineFrontBundle:refDoc')->encryptDoc($refDoc[0],$salt,true);
    }

}
