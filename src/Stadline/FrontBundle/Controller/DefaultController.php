<?php

namespace Stadline\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Doctrine\ORM\Mapping as ORM;
use Stadline\FrontBundle\Entity;

class DefaultController extends Controller
{
    public function hashAction($refContact)
    {
        $salt = $this->container->getParameter('secret');
        $em = $this->getDoctrine()->getManager();
        $contactRepository = $em->getRepository('StadlineFrontBundle:Contact');

        $hashedRef = $contactRepository->encrypt($refContact, $salt, true);
        if ($hashedRef === false) {
            throw new AuthenticationException('Could not find User');
        }

        $sugarClient = $this->get('stadline_sugar_crm_client');
        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");

        $sugarId = null;
        $sugarName = null;
        foreach ($accounts as $account) {
            if($account->getName() == "Ocea"){
                $sugarId = $account->getId();
                $sugarName = $account->getName();
            }
        }

        if(!is_null($sugarId)){
            $contact = $contactRepository->findOneBy(array('ref' => $refContact));
            $contact->setSugarAccountId($sugarId);
            $contact->setName($sugarName);

            $em->persist($contact);
            $em->flush();
        } else {
            $this->addFlash(
                'warning',
                'Warning : No client found for this name.'
            );
        }

        return $this->redirect($this->generateUrl('stadline_front_factures', array(
            'hashedRef' => $hashedRef
        )));
    }

    public function facturesAction($hashedRef)
    {
        $ref = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->decrypt($hashedRef);

        if ($ref === false) {
            throw new AuthenticationException('Could not find User');
        }

        $soapService = $this->getSoapService();
        $factures = $soapService->getFacturesByRefClient($ref); // 'C-000000-00033'




        $displayfactures = [];
        $refDocEncrypt = [];
        foreach ($factures as $index => $facture) {
            $nomContact = $facture["nom_contact"];
            $factures[$index]['__detail'] = $soapService->getDocument($facture['ref_doc']);
            if ($factures[$index]['type_doc'] == 4 and $factures[$index]['etat_doc'] != 16) {
                $displayfactures[$index] = $factures[$index];
                $salt = $this->container->getParameter('secret');
                $refDoc = $factures[$index]["ref_doc"];
//                var_dump($factures[$index]["ref_doc"]);
//                var_dump($factures[$index]["ref_doc"]);
                $refDocEncrypt[$index] = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:refDoc')->encryptDoc($refDoc,$salt,true);
            }
        }

        return $this->render('StadlineFrontBundle:Default:index.html.twig', array(
            'factures' => $displayfactures,
            'nom_contact' => $nomContact,
            'refDocEncrypt' => $refDocEncrypt

        ));
    }

    public function pdfAction($refDocEncrypt)
    {

        $refDoc = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:refDoc')->decryptDoc($refDocEncrypt);
        
        $soapService = $this->getSoapService();
        $binaire = $soapService->getPdfDocument($refDoc); // 'C-000000-00033'

        $nonbinaire = base64_decode($binaire);

        $filename = $refDoc;
//           file_put_contents($filename,$nonbinaire);
// Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('content-type', 'application/pdf');
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');


// Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent($nonbinaire);

        return $response;

    }

    private function getSoapService()
    {
        return $this->get('stadline_front.soap_service');
    }

    public function stateAction($hashedRef, $state)
    {
        $ref = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->decrypt($hashedRef);

        if ($ref === false) {
            throw new AuthenticationException('Could not find Client');
        }

        $soapService = $this->getSoapService();
        $factures = $soapService->getFacturesByRefClient($ref); // 'C-000000-00033'
        $displayfactures = [];
        foreach ($factures as $index => $facture) {
            $factures[$index]['__detail'] = $soapService->getDocument($facture['ref_doc']);
            $nomContact = $facture["nom_contact"];
            if ($factures[$index]['type_doc'] == 4 and $factures[$index]['etat_doc'] == $state)
                $displayfactures[$index] = $factures[$index];
        }


        return $this->render('StadlineFrontBundle:Default:index.html.twig', array(
            'factures' => $displayfactures,
            'nom_contact' => $nomContact
        ));
    }


    private $facturesTest = array(
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        ),
        array(
            'ref' => 'ref facture 1',
            'lib_etat_doc' => 'en attente',
            'date_doc' => '01/01/2001',
            'montant_ttc' => '200'
        )
    );

    public function testSugarAction()
    {

        $sugarClient = $this->get('stadline_sugar_crm_client');

        $accounts = $sugarClient->getAccounts("accounts.account_type = 'Customer'");

        echo '<pre>';
        print_r($accounts);
        echo '</pre>';

        die();
    }

}
