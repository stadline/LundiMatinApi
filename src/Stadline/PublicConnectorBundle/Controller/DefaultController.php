<?php

namespace Stadline\PublicConnectorBundle\Controller;

use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function widgetAction($hash)
    {
        $zendeskClient = $this->get('stadline_zendesk_client');
        $contact =  $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->findOneByHashedRef($hash);

        $clientLmbId = null;
        $clientZendeskId = null;
        if(!is_null($contact)){
            $clientLmbId = $contact->getRef();
            $clientZendeskId = $contact->getZendeskOrganizationId();
        }

        $clientTickets = array();
        if(!is_null($clientZendeskId)) {
            /* Guzzle */
            $client = new Client('https://extraclub.zendesk.com');
            $client->setDefaultOption('auth', array('amandine.fournier@stadline.com', 'fOUrnIEr88', 'Basic'));
            $request = $client->get('/api/v2/users/'.$clientZendeskId.'/tickets/requested.json');
            $response = $request->send();

            $ticketsArray = $response->json();
            foreach ($ticketsArray['tickets'] as $ticketArray){
                if($ticketArray['requester_id'] == $clientZendeskId){
                    $clientTickets[] = $ticketArray;
                }
            }
        }

        return $this->render('StadlinePublicConnectorBundle:Default:index.html.twig', array(
            'client_lmb_id' => $clientLmbId,
            'hash_ref' => $hash,
            'clientTickets' => $clientTickets
        ));
    }
}

