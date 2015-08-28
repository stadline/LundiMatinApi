<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 25/08/15
 * Time: 15:21
 */




namespace Stadline\TasksBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class ManagerTelExportService
{

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getDataFromSugar()
    {

        $sugarClient = $this->container->get('stadline_sugar_crm_client');
        $contacts = $sugarClient->getContacts(null);


        return $contacts;
    }


    public function generateCsv($data)
    {
        $chemin = 'Sugar.csv';
        $delimiteur = ',';

        $fichier_csv = fopen($chemin, 'w+');
        fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));
        $header = array('group','surname','name','workPhone','workMobile','homePhone','homeMobile');
        fputcsv($fichier_csv, $header, $delimiteur);

        foreach($data as $lignes)
        {


            $ligne[0] = $lignes->getGroup();
            $ligne[1] = $lignes->getFirstname();
            $ligne[2] = $lignes->getLastname();
            $ligne[3] = $lignes->getWorkPhone();
            $ligne[4] = $lignes->getWorkMobile();
            $ligne[5] = $lignes->gethomePhone();
            $ligne[6] = $lignes->gethomeMobile();



            fputcsv($fichier_csv, $ligne, $delimiteur);


        }
        fclose($fichier_csv);

    }


    function sendEmail()
    {

    }





}