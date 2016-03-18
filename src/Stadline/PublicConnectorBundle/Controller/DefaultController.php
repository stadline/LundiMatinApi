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
        if(is_null($clientZendeskId)) {
            die('no Zendesk Id');
        } else {
            $tickets = $zendeskClient->tickets()->findAll();
            foreach ($tickets->{'tickets'} as $ticket){
                if($ticket->{'requester_id'} == $clientZendeskId){
                    $clientTickets[] = $ticket;
                }
            }
//
//            $url = "https://extraclub.zendesk.com/api/v2/users/".$clientZendeskId."/tickets/requested.json";
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL, $url);
//            curl_setopt($curl, CURLOPT_USERPWD, 'amandine.fournier@stadline.com:fOUrnIEr88');
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//            $tickets = curl_exec($curl);
//
//            $ticketsArray = json_decode($tickets);
//            curl_close($curl);
//
//            foreach ($ticketsArray->{'tickets'} as $ticketArray){
//                if($ticketArray->{'requester_id'} == $clientZendeskId){
//                    $clientTickets[] = $ticketArray;
//                }
//            }
        }

        return $this->render('StadlinePublicConnectorBundle:Default:index.html.twig', array(
            'client_lmb_id' => $clientLmbId,
            'hash_ref' => $hash,
            'clientTickets' => $clientTickets
        ));
    }
}

