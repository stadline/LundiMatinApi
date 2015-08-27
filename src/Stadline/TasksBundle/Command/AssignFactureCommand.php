<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 03/08/15
 * Time: 14:52
 */


namespace Stadline\TasksBundle\Command;

use Stadline\FrontBundle\StadlineFrontBundle;
use Stadline\SugarCRMBundle\Service\Client;
use Stadline\TasksBundle\Entity\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stadline\FrontBundle\Service\SoapService;

/**
 * Cette tache permet d'attribuer un numero de facture à toutes les affaires
 *
 * Class AssignFactureCommand
 * @package Stadline\TasksBundle\Command
 */
class AssignFactureCommand extends ContainerAwareCommand
{
    const SALE_STAGE_LOST = 'Closed Lost';
    const SALE_STAGE_WON = 'Closed Won';

    protected function configure()
    {
        $this
            ->setName('Stadline:assignfacture')
            ->setDescription('Assigner les numeros de factures de LundiMatin aux affaires de Sugar');

    }

    /**
     * Execute the task
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        // get sugar client
        /** @var  Client */
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");
        // get logs service
        $clientlog = $container->get('stadline_tasks.log');
        $date = date("Y-m-d H:i:s");


        $numclients = array();
        // pour chaque compte client, je récupère l'id du compte pour ensuite aller chercher les affaires.
        foreach ($accounts as $value) {
            $numclients[$value->getid()] = $value->getname();//on recupere tout les id des comptes clients de Sugar
            if($value->getname() == "Mobile Angelo") {
                $valeurtest[$value->getid()] = $value->getname();
            }
        }

        // valeur test de Lundi Matin.
//        $valeurtest[] = '7aa3a0b2-ebb8-108e-1bc6-55c895a8e326';

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
            $final = [];
            // boucle sur les factures pour matcher les montants
            foreach ($fact as $data) {
                // on associe les factures et affaires sans numero de facture en comparant leur montant , si ils sont pareils on selectionne la facture
                if ($value->getamount() == $data['details']['net_ht'] and $value->getidLMB() == $data['ref_contact']) {
                    $final[] = $data;
                }
            }

            // pour faire la mise à jour il faut qu'une et une seule facture soit associée à une affaire si c'est le cas on fait la mise à jour
            if (!empty($final[0]) and empty($final[1])) {

                // identifiant du PDF
                $refDoc = $clientlog->getpdf($final[0]['ref_doc']);
                $pdf = 'http://billing.stadline.com/facture/pdf/'.$refDoc[0]; // on met un lien pour que le client puisse telecharger sa facture

                $query = array(array("name" => "id", "value" => $value->getid()),
                    array("name" => "num_lmb_fact_c", "value" => $final[0]['ref_doc']),
                    array("name" => "pdf_lmb_fact_c", "value" => $pdf));

                $sugarClient->setOpportunities($query); // on fait la mise à jour sur sugar
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

            $output->writeln('> '.$value->getname().' : '.$alerte.' - '.$maj);
            $clientlog->getValueAssign($value->getname(), $date, $alerte, $maj); // on envoit les logs
        }
    }
}