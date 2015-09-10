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
        $contacts = $sugarClient->getContacts(null); // on prend tous les contacts

        return $contacts;
    }


    public function generateCsv($data)
    {
        $chemin = 'Sugar.csv';
        $delimiteur = ',';

        $csvFile = fopen($chemin, 'w+');
        fprintf($csvFile, chr(0xEF).chr(0xBB).chr(0xBF));
        $header = array('group','surname','name','workPhone','workMobile','homePhone','homeMobile');
        fputcsv($csvFile, $header, $delimiteur);

        $row = array();
        foreach($data as $contact)
        {
            $WorkPhone = $contact->getWorkPhone();
            if(preg_match('#^([1-9][ ]?){9}$#',$WorkPhone)) //certain numero ne sont pas valides
            {
                $WorkPhone = '0'.$WorkPhone;
            }
            $WorkMobile = $contact->getWorkMobile();
            if(preg_match('#^([1-9][ ]?){9}$#',$WorkMobile)) //certain numero ne sont pas valides
            {
                $WorkMobile = '0'.$WorkMobile;
            }

            $row[0] = $contact->getGroup();
            $row[1] = $contact->getFirstname();
            $row[2] = $contact->getLastname();
            $row[3] = $WorkPhone;
            $row[4] = $WorkMobile;
            $row[5] = $contact->gethomePhone();
            $row[6] = $contact->gethomeMobile();

            // update csv with new line
            fputcsv($csvFile, $row, $delimiteur);
        }

        // close file
        fclose($csvFile);

    }


    function sendEmail()
    {

    }

}