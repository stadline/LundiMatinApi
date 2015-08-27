<?php

namespace Stadline\TasksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StadlineTasksBundle:Default:index.html.twig', array('name' => $name));
    }


    public function PageLogAction()
    {
        $client = $this->get('stadline_tasks.log');
        $data = $client->getAlldata('StadlineTasksBundle:Logger');


        foreach ($data as $data)
        {
            $RefAffaire[] = $data->getRefAffaire();
            $date[] = $data->getDate();
            $erreur[] = $data->getErreur();
            $maj[] = $data->getMaj();
        }


        return $this->render('StadlineTasksBundle:Default:index.html.twig', array(
            'RefAffaire' => $RefAffaire,
            'date' => $date,
            'erreur' => $erreur,
            'maj' => $maj
        ));
    }


    public  function PageLogErrorAction()
    {
        $client = $this->get('stadline_tasks.log');
        $data = $client->getAlldata('StadlineTasksBundle:Logger');


        foreach ($data as $data)
        {
            if($data->getErreur() == 'erreur montant' )
            {
                $RefAffaire[] = $data->getRefAffaire();
                $date[] = $data->getDate();
                $erreur[] = $data->getErreur();
                $maj[] = $data->getMaj();
            }

        }


        return $this->render('StadlineTasksBundle:Default:index.html.twig', array(
            'RefAffaire' => $RefAffaire,
            'date' => $date,
            'erreur' => $erreur,
            'maj' => $maj
        ));

    }


    public function assignfactureAction()
    {
        $client = $this->get('stadline_tasks.log');
        $data = $client->getAlldata('StadlineTasksBundle:AssignfactureLog');

        foreach ($data as $value)
        {

            $Ref[] = $value->getRef();
            $date[] = $value->getDate();
            $erreur[] = $value->getErreur();
            $maj[] = $value->getMaj();
        }


        return $this->render('StadlineTasksBundle:Default:assignefacturelog.html.twig', array(
            'Ref' => $Ref,
            'date' => $date,
            'erreur' => $erreur,
            'maj' => $maj));
    }

}
