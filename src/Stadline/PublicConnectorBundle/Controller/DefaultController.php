<?php

namespace Stadline\PublicConnectorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function widgetAction($clientId)
    {
        $zendeskClient = $this->get('stadline_zendesk_client');

        return $this->render('StadlinePublicConnectorBundle:Default:index.html.twig', array('client_id' => $clientId));
    }
}
