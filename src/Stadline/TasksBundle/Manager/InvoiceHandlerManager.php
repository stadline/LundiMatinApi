<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 03/08/15
 * Time: 14:52
 */


namespace Stadline\TasksBundle\Manager;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class InvoiceHandlerManager
{
    const SALE_STAGE_LOST = 'Closed Lost';
    const SALE_STAGE_WON = 'Closed Won';
    const DATE_INTERVALLE = '+90';

    private $container = null;

    public function __construct($container) {
        $this->container = $container;
    }

    public function executeAssignFactureCommand($output)
    {
        // client pour lundiMatin
        $client = $this->getLundiMatinClient();
        $ManagerCommand = $this->getCommandManager();
        $numclients = $this->getSugarClientNumber();
        $affaires = $this->extractAffairesFromSugar($output, $numclients);
        $date = date("Y-m-d H:i:s");

        // si j'ai aucune affaire sans num de facture, je peux arréter le script
        if(empty($affaires['without_num'][0])) {
            $output->writeln('> aucune mise à jour nécessaire');
            exit;
        }

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
                    $output->writeln('> Facture inconnue sur Sugar trouvée : '.$data['ref_doc'].' pour le montant HT de '.$ht);
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
            $message = null;

            $assignFilters = array(
                'AssignFilterOk',
                'AssignFilterSomeFactureSameAmount',
                'AssignFilterSomeAffairSameAmount',
                'AssignFilterSomeAffaireSameAmountNoFacture',
            );

            foreach($assignFilters as $assignFilter) {
                $assign = new AssignFacture(new $assignFilter());
                if($assign instanceof ContainerAwareInterface) {
                    $assign->setContainer($this->container);
                }

                $assignMessage = $assign->executeAssignFactureMatch($montantfacture, $amount, $affaires);
                if($assignMessage != null){
                    $message = $assignMessage;
                    break;
                }
            }

            if($message == null){
                $alerte = 'aucun montant ne correspond à cette affaire';
                $maj = 'pas de mise à jour';
                $message = array('alerte' => $alerte, 'maj' => $maj);
            }

            $output->writeln('> '.$affaires[0]["name"].' : '.$message['alerte'].' - '.$message['maj']);
            $ManagerCommand->createNewLogEntry($affaires[0]["name"], $date, $message['alerte'], $message['maj']); // on envoit les logs
        }
    }

    public function executeStructurePhaseCommand($output)
    {
        // get sugar client
        /** @var  Client */
        $sugarClient = $this->getSugarClient();
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");
        $date = date("Y-m-d H:i:s");
        $ManagerCommand = $this->getCommandManager();

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
        $client = $this->getLundiMatinClient();
        // Pour chaque compte de Sugar on récupere toutes ses affaires et on ne selectionne que celles qui ne sont pas perdue ou payée
        foreach ($valeurtest as $clientId => $clientName) //
        {
            $opportunities = $sugarClient->getOpportunities($clientId);

            if (count($opportunities) > 0) {
                foreach ($opportunities as $salesStage) {

                    if ( $salesStage->getSalesStage() != self::SALE_STAGE_LOST && $salesStage->getSalesStage() != self::SALE_STAGE_WON && $salesStage->getnumfact() != '') {
                        $output->writeln('> Affaire avec numéro LM trouvée pour '.$clientName.' : '.$salesStage->getname());
                        $affaires[] = $salesStage;
                        $ref = $salesStage->getnumfact();
                        $factures[] = $client->getFacturesByRef($ref);
                    }
                    else {
                        $output->writeln('> Affaire sans numéro LM trouvée pour ' . $clientName . ' : ' . $salesStage->getname());
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
                    $message = null;

                    $structureFilters = array(
                        'SalesStageAFacturer',
                        'SalesStageClosedLost',
                        'SalesStageFacture',
                        'SalesStageClosedWon',
                    );

                    foreach($structureFilters as $structureFilter) {
                        $structure = new StructurePhase(new $structureFilter());
                        $structureMessage = $structure->executeStructurePhaseMatch($value, $data, $sugarClient);
                        if($structureMessage != null){
                            $message = $structureMessage;
                            break;
                        }
                    }
                    if($message == null){
                        $erreur = 'erreur etat';
                        $maj = 'pas de mise à jour';
                        $message = array('erreur' => $erreur, 'maj' => $maj);
                    }
                }
                else{
                    $erreur = 'erreur montant';
                    $maj = 'pas de mise à jour';
                    $message = array('erreur' => $erreur, 'maj' => $maj);
                }

                $output->writeln('> '.$data->getname().' : '.$message['erreur'].' - '.$message['maj']);
                $ManagerCommand->getValue($factures[$index][0]['ref_doc'],$date,$message['erreur'],$message['maj']);// on envoie les logs
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

    public function extractAffairesFromSugar($output, $numclients) {
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
                            $output->writeln('> Affaire sans numéro LM trouvée pour '.$clientName.' : '.$salesStage->getname());
                        } else {
                            $output->writeln('> Affaire avec numéro LM trouvée pour '.$clientName.' : '.$salesStage->getname());
                        }
                    }
                }
            }
        }

        $affaires['idLMB'] = array_values(array_unique($affaires['idLMB']));// on fait en sorte d'enlever les doublons et on supprime les index.

        return $affaires;
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

    public function refDocEncrypt($refDoc) {
        $salt = $this->container->getParameter('secret');
        return $this->container->get('doctrine.orm.default_entity_manager')->getRepository('StadlineFrontBundle:refDoc')->encryptDoc($refDoc[0],$salt,true);
    }

}
