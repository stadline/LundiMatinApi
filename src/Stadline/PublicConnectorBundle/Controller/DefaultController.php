<?php

namespace Stadline\PublicConnectorBundle\Controller;

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

        return $this->render('StadlinePublicConnectorBundle:Default:index.html.twig', array(
            'client_lmb_id' => $clientLmbId,
            'hash_ref' => $hash
        ));
    }
}
