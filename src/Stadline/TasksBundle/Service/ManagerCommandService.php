<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 27/08/15
 * Time: 15:35
 */

namespace Stadline\TasksBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManager;
use Stadline\TasksBundle\Entity\AssignfactureLog;
use Stadline\TasksBundle\Entity\Logger;


class ManagerCommandService

{

    private $container;
    private $em;

    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function getpdf($refDoc)
    {
        $salt = $this->container->getParameter('secret');
        $refDocEncrypt = $this->em->getRepository('StadlineFrontBundle:refDoc')->encryptDoc($refDoc, $salt, true);
        return $refDocEncrypt;
    }

    public function getclient($refclient)
    {
        $salt = $salt = $this->container->getParameter('secret');

        $refclientEncrypt[] = $this->em->getRepository('StadlineFrontBundle:Contact')->encrypt($refclient, $salt, true);

        return $refclientEncrypt;
    }

    /**
     * @deprecated
     *
     * @param $affaire
     * @param $date
     * @param $erreur
     * @param $maj
     */
    public function getValueAssign($affaire, $date, $erreur, $maj)
    {
        return $this->createNewLogEntry($affaire, $date, $erreur, $maj);
    }

    public function createNewLogEntry($affaire, $date, $erreur, $maj)
    {

        $log = new AssignfactureLog ();
        $log->setRef($affaire);

        $log->setDate($date);
        $log->setErreur($erreur);
        $log->setMaj($maj);

        $em = $this->em;

        $em->persist($log);
        $em->flush();
    }


    public function getAlldata($entity)
    {
        $entities = $this->em
            ->getRepository($entity)
            ->findAll();
        if (!$entities) {
            throw $this->createNotFoundException(
                'Aucun produit trouvé'
            );
        }

        return $entities;

    }

    function getValue($affaire, $date, $erreur, $maj)
    {

        $logger = new Logger();
        $logger->setRefAffaire($affaire);

        $logger->setDate($date);
        $logger->setErreur($erreur);
        $logger->setMaj($maj);


        $em = $this->em;


        $em->persist($logger);

        $em->flush();
    }
}