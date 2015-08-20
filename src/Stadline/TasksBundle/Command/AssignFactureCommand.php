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
            ->setDescription('Assigner les numeros de factures');

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $sugarClient = $container->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts();
        //var_dump($sugarClient->getOpportunities('1249b119-216a-c5f1-a10b-452a99501b77'));
        //var_dump($sugarClient->getOpportunities('6c514565-295c-92ba-a9fa-52f9fd1a7ae5'));

        //var_dump($accounts);


        foreach ($accounts as $value) {
            $data[] = $value->getid();
        }

        unset($data[5]);
        $data = array_values($data); // $data contient les numero de clients
        // on enleve le 7 77  qui ne sert à rien


        $valeurtest[] = '7aa3a0b2-ebb8-108e-1bc6-55c895a8e326';


        foreach ($valeurtest as $value) // on cherche à  obtenir les phase de vente pour chaque affaires
        {

            $opportunities = $sugarClient->getOpportunities($value);// on recupere toutes les affaires

            if (count($opportunities) > 0) {
                foreach ($opportunities as $SalesStage) {

                    if ($SalesStage->getSalesStage() != 'Closed Won' and $SalesStage->getSalesStage() != 'Closed Lost') {
                        $affaires[] = $SalesStage;
                    }

                }
            }

        }
        $affaire_sansnum = [];
        foreach($affaires as $value)
        {
            if( $value->getnumfact() == '')
            {
                $affaire_sansnum[] =  $value;
            }
        }

        foreach ($affaires as $value) { // on recupere les numeros de clients de LundiMatin

            $idLMB[] = $value->getidLMB();
        }
        $idLMB = array_unique($idLMB);
        $idLMB = array_values($idLMB);
        $client = $container->get('stadline_front.soap_service');

        foreach ($idLMB as $value) {

            $factures[] = $client->getFacturesByRefClient($value); //on recupere les factures de LundiMatin
        }

        foreach($affaires as $value)
        {
               
        }

        $compt = 0;
        foreach ($factures[0] as $value) {
            $facture[] = $client->getDocument($value['ref_doc']);
            $compt += 1;
            var_dump($compt);
        }


        foreach ($affaires as $data) { // on compare les montant des affaires et des factures
            $tab = [];
            foreach ($facture as $value) {

                if ($value['montant_ttc'] == $data->getamount()) {
                    $tab[] = $value;

                }

            }
            if(isset($tab[1])) //si elle est unique
            {
                unset($tab);
            }
            elseif($tab != []) { // si elle existe


                $query = array("id" => $data->getid(),
                    "update" => $tab[0]['ref_doc']); // on veut renvoyer notre numero de facture vers Sugar

                var_dump($query);
                die();
                $sugarClient->setOpportunities($query);

            }
        }

    }

}