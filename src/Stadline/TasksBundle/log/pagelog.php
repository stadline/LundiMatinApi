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

        $logger = new Logger();
        $logger->setRefAffaire($affaire);

        $logger->setDate($date);
        $logger->setErreur($erreur);
        $logger->setMaj($maj);


        $em = $this->getDoctrine()->getManager();



        $em->persist($logger);

        $em->flush();
    }



    public function getAlldata()

    {
        $logger = $this->getDoctrine()
            ->getRepository('StadlineTasksBundle:Logger')
            ->findAll();
        if (!$logger) {
        throw $this->createNotFoundException(
            'Aucun produit trouvé'
        );
        }
        return $logger;

    }

}