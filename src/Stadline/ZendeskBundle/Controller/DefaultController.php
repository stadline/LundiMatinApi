<?php

namespace Stadline\ZendeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StadlineZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
