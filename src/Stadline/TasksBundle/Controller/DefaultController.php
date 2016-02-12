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
        $client = $this->get('stadline_tasks.ManagerCommand');
        $datas = $client->getAlldata('StadlineTasksBundle:Logger');

        return $this->render('StadlineTasksBundle:Default:index.html.twig', array(
           'datas' => $datas
        ));
    }


    public  function PageLogErrorAction()
    {
        $client = $this->get('stadline_tasks.ManagerCommand');
        $datas = $client->getAlldata('StadlineTasksBundle:Logger');

        return $this->render('StadlineTasksBundle:Default:errorlog.html.twig', array(
            'datas' => $datas
        ));
    }


    public function assignfactureAction()
    {
        $client = $this->get('stadline_tasks.ManagerCommand');
        $datas = $client->getAlldata('StadlineTasksBundle:AssignfactureLog');

        return $this->render('StadlineTasksBundle:Default:assignefacturelog.html.twig', array(
            'datas' => $datas
        ));
    }

}
