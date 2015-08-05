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
        $data = $client->getAlldata();


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
        $data = $client->getAlldata();


        foreach ($data as $data)
        {
            if($data->getErreur() == 'date de creation incorrect' or $data->getErreur() == 'montant ttc incorrect')
            {
                $RefAffaire[] = $data->getRefAffaire();
                $date[] = $data->getDate();
                $erreur[] = $data->getErreur();
                $maj[] = $data->getMaj();
            }

        }


        return $this->render('StadlineTasksBundle:Default:index.html.twig', array(
            'date' => $date,
            'RefAffaire' => $RefAffaire,
            'erreur' => $erreur,
            'maj' => $maj
        ));
    }

}
