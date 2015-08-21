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
        $clientlog = $container->get('stadline_tasks.log');
        $date = date("Y-m-d H:i:s");
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

        if(empty($affaire_sansnum[0]))
        {
            var_dump('aucune mise à jour nécessaire');
            die();
        }



        foreach ($affaire_sansnum as $value) { // on recupere les numeros de clients de LundiMatin

            $idLMB[] = $value->getidLMB();
            $id[] = $value->getid();
        }
        $idLMB = array_unique($idLMB);
        $idLMB = array_values($idLMB);
        $client = $container->get('stadline_front.soap_service');



        foreach ($idLMB as $value)
        {

            $ALLfactures [] = $client->getFacturesByRefClient($value);
            foreach($affaires as $data)
            {
                $num[] = $data->getnumfact();
            }

            foreach($ALLfactures[0] as $value)
            {
                if(!in_array($value['ref_doc'],$num))
                {
                    $fact[] = $value;
                }
            }
        }




        $compt = 0;
        foreach ($fact as $index=>$value) {
            $fact[$index]['details'] = $client->getDocument($value['ref_doc']);
            $compt += 1;
            var_dump($compt);
        }

        foreach($affaire_sansnum as $value)
        {
            $final=[];
            foreach($fact as $data)
            {
                if($value->getamount()== $data['details']['montant_ttc'])
                {
                    $final[] = $data;
                }
            }

            if(!empty($final[0]) and empty($final[1]))
            {

                $refDoc = $clientlog->getpdf($final[0]['ref_doc']);


                $pdf = 'http://billing.stadline.com/facture/pdf/';
                $pdf.=$refDoc[0];

                $query =array( array("name" => "id", "value" => $value->getid()),
                        array("name" => "num_lmb_fact_c" ,"value" => $final[0]['ref_doc']),
                        array("name"=>"pdf_lmb_fact_c" ,"value" =>$pdf));


                $test = $sugarClient->setOpportunities($query);
                $maj = 'mise à jour effectuée';
                $alerte = 'aucune erreur';
            }

            elseif(!empty($final[1]))
            {
                $alerte = 'il y a plusieurs factures ayant le même montant que cette affaire';
                $maj = 'pas de mise à jour';
            }

            else
            {
                $alerte = 'aucun montant ne correspond à cette affaire';
                $maj = 'pas de mise à jour';
            }
            var_dump($maj);
            var_dump($alerte);
            $clientlog->getValueAssign($value->getname(),$date,$alerte,$maj);
        }



        die();



    }

}