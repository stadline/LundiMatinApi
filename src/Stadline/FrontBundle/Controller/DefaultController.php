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
        $hashedRef = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->encrypt($refContact, $salt, true);
        if ($hashedRef === false) {
            throw new AuthenticationException('Could not find User');
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
                $refDocEncrypt[] = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:refDoc')->encryptDoc($refDoc,$salt,true);
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


        //$accounts = $sugarClient->getAccounts();
        //$sugarClient->getOpportunities('1249b119-216a-c5f1-a10b-452a99501b77');
        //var_dump($sugarClient->getOpportunities('7aa3a0b2-ebb8-108e-1bc6-55c895a8e326')); //Client test
        $affairetest = $sugarClient->getOpportunities('7aa3a0b2-ebb8-108e-1bc6-55c895a8e326');
        $soapService = $this->getSoapService();
        $test = $soapService->getFacturesByRefClient('C-000000-00005');

        foreach($test as $index=>$value)
        {
            $test[$index]['details'] = $soapService->getDocument($value['ref_doc']);
            var_dump($test[$index]['details']['montant_ttc']);
        }


        die();
        $account = $sugarClient->getAccounts();

        $affaires = [];
        foreach ($affairetest as $value) {
            if ($value->getSalesStage() != 'Closed Won' and $value->getSalesStage() != 'Closed Lost' and $value->getnumfact() == '') {
                $affaires [] = $value;
            }


        }

        foreach($affaires as $value)
        {

            $idLMB[] = $value->getidLMB();
        }
        $idLMB = array_unique($idLMB);
        $idLMB = array_values($idLMB);
        $soapService = $this->getSoapService();
        foreach($idLMB as $value)
        {

            $factures[] = $soapService->getFacturesByRefClient($value);
        }

        $soapService = $this->getSoapService();
        foreach($factures[0] as $value) {
            $facture[] = $soapService->getDocument($value['ref_doc']);

        }


        foreach($affaires as $data)

        {
            $tab = [];
            foreach($facture as $value)
            {

                if($value['montant_ttc'] == $data->getamount())
                {
                    $tab[] = $value;

                }
            }
            $finalfact[] = $tab;

        }

        foreach($finalfact as $key => $value)
        {
            if(isset($value[1]))
            {
                unset($finalfact[$key]);

            }
        }
        $finalfact = array_values($finalfact);
        var_dump($finalfact);
        die();

    }


}
