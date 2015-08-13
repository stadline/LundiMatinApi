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
        $accounts = $sugarClient->getAccounts();
        //var_dump($sugarClient->getOpportunities('1249b119-216a-c5f1-a10b-452a99501b77'));
        //var_dump($sugarClient->getOpportunities('6c514565-295c-92ba-a9fa-52f9fd1a7ae5'));

        //var_dump($accounts);


        foreach($accounts as $value)
        {
            $data[] = $value->getid();
        }

        unset($data[5]);
        $data = array_values($data); // $data contient les numero de clients
        // on enleve le 7 77  qui ne sert à rien



        foreach($data as $value) // on cherche à  obtenir les phase de vente pour chaque affaires
        {
            //var_dump($value);
            $opportunities = $sugarClient->getOpportunities($value);// on recupere toutes les affaires
            //var_dump($test);
            if(count($opportunities) > 0 )
            {
                foreach($opportunities as $SalesStage)
                {

                    if($SalesStage->getSalesStage() != 'Closed Won' and $SalesStage->getSalesStage() != 'Closed Lost')
                    {
                        $affaires[] = $SalesStage;
                    }

                }
            }

        }
        $affaire = []; // correspond au affaire non payé et non perdu

        foreach($affaires as $value)
        {
            if($value->getnumfact() != '')
            {
                $affaire[] = $value;
            }
        }

        // on recupere les factures de lundi matin correspondant au numero de facture des affaires dans sugar
        $client = $container->get('stadline_front.soap_service');
        $factures = $client->getFacturesByRef($affaire['numfact']);
        var_dump($factures);
        die();




















        $affaire = $input->getArgument('affaire');

        $date_creation_affaire = "2015-05-05 11:53:16";
        $montant_ttc_affaire = '8760';
        $etat_affaire = 'en attente';





        $container = $this->getContainer();
        $client = $container->get('stadline_front.soap_service');
        $factures = $client->getFacturesByRef($affaire);

        $date_maj = date('Y-m-d H:i:s');
        $mise_a_jour = 'aucune mise à jour effectuée';
        $message_erreur = 'aucune erreur';



        foreach ($factures as $index => $facture)
        {
           $factures[$index]['__detail'] = $client->getDocument($facture['ref_doc']);
            $montant_ttc = $factures[$index]['__detail']['montant_ttc'];
            $date_creation = $facture['date_creation_doc'];
            $etat_doc = $facture['etat_doc'];

        }


        // comparer les infos de LundiMatin et celles recuperées
        $client = $container->get('stadline_tasks.log');
        $message_erreur = $client->verification($date_creation,$date_creation_affaire,$montant_ttc,$montant_ttc_affaire);

        if($message_erreur == 'aucune erreur'){

            if($etat_doc != $etat_affaire and $etat_doc == 19) // si Acquittée
            {
                $etat_affaire  = 'payée';
                $mise_a_jour = 'mise à jour effectuée';
            }

            elseif ($etat_doc != $etat_affaire and $etat_doc  == 18) // si à regler
            {
                $etat_affaire = 'facturée';
                $mise_a_jour = 'mise à jour effectuée';
            }
        }




            $client->getValue($affaire,$date_maj,$message_erreur,$mise_a_jour);


            $data = $client->getAlldata();
            var_dump($etat_affaire);




    }

}