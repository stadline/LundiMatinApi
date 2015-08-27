<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 30/07/15
 * Time: 17:38
 */

namespace Stadline\TasksBundle\log;


use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Stadline\TasksBundle\Entity\Logger;
use Stadline\TasksBundle\Entity\AssignfactureLog;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;


class pagelog extends controller
{


    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    function getValue ($affaire,$date,$erreur,$maj)
    {
        die();
        $logger = new Logger();
        $logger->setRefAffaire($affaire);

        $logger->setDate($date);
        $logger->setErreur($erreur);
        $logger->setMaj($maj);


        $em = $this->getDoctrine()->getManager();



        $em->persist($logger);

        $em->flush();
    }



    public function getAlldata($entity)

    {
        $logger = $this->getDoctrine()
            ->getRepository($entity)
            ->findAll();
        if (!$logger) {
        throw $this->createNotFoundException(
            'Aucun produit trouvé'
        );
        }
        return $logger;

    }

    function getAffaire($montantHT,$Sugar)
    {
        $Facture = [];

        foreach($Sugar as $sugar)
        {
            if($sugar['montant_ht'] == $montantHT)
                $Facture = $sugar;
        }

        if(!isset($Facture[0]))
        {
            $message = 'alert aucune facture trouvée';
            return $message;
        }

        elseif(isset($Facture[1]))
        {
            $message = 'alert plusieurs factures trouvées';
            return $message;
        }

        else
        {
            return $Facture;
        }
    }


    function verification($date_creation,$date_creation_affaire,$montant_ttc,$montant_ttc_affaire)
    {
        $message_erreur = 'aucune erreur';
        if ($date_creation != $date_creation_affaire)
        {
            $message_erreur = 'date de creation incorrect';
            echo $message_erreur;

        }

        elseif ($montant_ttc != $montant_ttc_affaire)
        {
            $message_erreur = 'montant ttc incorrect';
            echo $message_erreur;

        }

        return $message_erreur;
    }



    public function Sugar()
    {

        $sugarClient = $this->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts();

        $account = [];
        foreach ($accounts as $value) {
            $data[] = $value->getid();
        }


        foreach ($data as $value) {
            $opportunities[] = $sugarClient->getOpportunities($value);
        }


        return $opportunities;
    }


    public function getValueAssign($affaire,$date,$erreur,$maj)
    {
        die();
        $log = new AssignfactureLog ();
        $log->setRef($affaire);

        $log->setDate($date);
        $log->setErreur($erreur);
        $log->setMaj($maj);


        $em = $this->getDoctrine()->getManager();



        $em->persist($log);

        $em->flush();
    }

    public function getpdf($refDoc)
    {
        $salt = $this->container->getParameter('secret');
        $refDocEncrypt[] = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:refDoc')->encryptDoc($refDoc,$salt,true);
        return $refDocEncrypt;
    }

    public function getPageClient($refContact)
    {
        $salt = $this->container->getParameter('secret');
        $hashedRef = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->encrypt($refContact, $salt, true);
        return $hashedRef;
    }
}