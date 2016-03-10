<?php

namespace Stadline\PublicConnectorBundle\Controller;

use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function widgetAction($hash)
    {
//        $zendeskClient = $this->get('stadline_zendesk_client');

        /* Zendesk get auth */
//        $auth = $zendeskClient->getAuthorization(array("response_type" => "code", "redirect_uri" => "http://lundimatin.local/app_dev.php/widget/1/links", "client_id" => $clientId, "scope" => "read write"))->getObjectResponse();
//        var_dump($auth);
//        die();

//        $auth = $zendeskClient->getAuth();
//        var_dump($auth);
//        die();

        $em = $this->get('doctrine.orm.entity_manager');

        $contact = $em
            ->getRepository("StadlineFrontBundle:Contact")
            ->findOneByHashedRef($hash)
        ;

        $clientLmbId = null;
        if($contact != null){
            $clientLmbId = $contact->getRef();
        }


        /* Test Guzzle */
        $client = new Client();
        $request = $client->get('https://extraclub.zendesk.com/oauth/authorizations/new');
        $query = $request->getQuery();
        $query->add("client_id","extraclub");
        $query->add("response_type","code");
        $query->add("redirect_uri","https://extraclub.zendesk.com/hc/fr/requests");
        $query->add("scope","read write");

        $client2 = new Client();
        $request2 = $client2->get('https://extraclub.zendesk.com/api/v2/tickets.json', array("Authorization" => "Bearer ac4432ba2f8bfca69f956ba988a3d03b69696cb44d04e963e73c9cc7de8db91a"), array());

        $response = $request2->send();

//        var_dump($response);
//        die();

        return $this->render('StadlinePublicConnectorBundle:Default:index.html.twig', array(
            'client_lmb_id' => $clientLmbId,
            'hash_ref' => $hash
        ));
    }
}

