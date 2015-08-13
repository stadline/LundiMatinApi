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
            if($value->getnumfact() == '')
            {
                $affaire[] = $value;

            }
        }


        var_dump($affaire);
        die();


































        $Sugar = array();

        $ListeAffaires= [];
        $Affaires = [];
        $montant_ht = '1000';

        foreach($Sugar as $data )
        {
            if($data['phase_de_vente'] != "payé" or $data['phase_de_vente'] != "perdu")
            {
                $ListeAffaires = $data;
            }
        }

        foreach($ListeAffaires as $data)
        {
            if(!isset($data['ref_doc']))
            {
                $Affaires = $data;
            }
        }

        $container = $this->getContainer();
        $client = $container->get('stadline_front.soap_service');
        $ref_client_affaire = [];
        foreach($Affaires as $affaire)
        {
            $ref_client_affaire =$affaire['ref_client'];
        }



        $factures = $client->getAllFactures();
        foreach($factures as $facture)
        {
            $ref_doc[] = $facture['ref_doc'];
            $ref_contact[] = $facture['ref_contact'];
        }

        //Apres avoir fait le diffrentiel on obtient une liste de factures qui ne sont pas dans les affaires de Sugar , on l'appelle ici Liste

        $client = $container->get('stadline_tasks.log');
        $facture = $client->getAffaire($montant_ht,$Affaires);
        $message_erreur = $client->verification();

    }
}