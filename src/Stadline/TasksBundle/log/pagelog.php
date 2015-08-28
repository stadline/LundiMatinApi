<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 30/07/15
 * Time: 17:38
 */

namespace Stadline\TasksBundle\log;


use Doctrine\Common\Util\Debug;
use Stadline\TasksBundle\Entity\Logger;
use Stadline\TasksBundle\Entity\AssignfactureLog;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;


class pagelog
{

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }




    public function getAffaire($montantHT,$Sugar)
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






    public function getPageClient($refContact)
    {
        $salt = $this->container->getParameter('secret');
        $hashedRef = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->encrypt($refContact, $salt, true);
        return $hashedRef;
    }
}