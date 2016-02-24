<?php

namespace Stadline\PublicConnectorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function widgetAction($clientId)
    {
        return $this->render('StadlinePublicConnectorBundle:Default:index.html.twig', array('name' => $clientId));
    }
}
