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
            ->setDescription('Mettre à jour les factures')
            ->addArgument('affaire', InputArgument::REQUIRED, 'la référence de facture')
            ->addArgument('date_creation_affaire', InputArgument::REQUIRED, 'la date de creation de la facture')
            ->addArgument('montant_ttc_affaire', InputArgument::REQUIRED, 'le montant de la facture');

    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }





    protected function execute(InputInterface $input, OutputInterface $output)
    {

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