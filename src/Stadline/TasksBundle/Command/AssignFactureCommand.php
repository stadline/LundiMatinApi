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
        $input = array();

        $ListeAffaires= [];
        $Affaires = [];

        foreach($input as $data )
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
        $facture = $client->getAffaire();

    }
}