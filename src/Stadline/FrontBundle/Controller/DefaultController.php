<?php

namespace Stadline\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Doctrine\ORM\Mapping as ORM;


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
        foreach($factures as $index => $facture) {
            $nomContact = $facture["nom_contact"];
            $factures[$index]['__detail'] = $soapService->getDocument($facture['ref_doc'] );
            if ($factures[$index]['type_doc'] == 4 and $factures[$index]['etat_doc'] != 16)
            {
                $displayfactures[$index] = $factures[$index];
            }
        }




        return $this->render('StadlineFrontBundle:Default:index.html.twig', array(
            'factures' => $displayfactures,
            'nom_contact' => $nomContact
        ));
    }
    
    public function pdfAction($refDoc)
    {
        $soapService = $this->getSoapService();
        $binaire = $soapService->getPdfDocument($refDoc); // 'C-000000-00033'

        $nonbinaire=base64_decode($binaire);

        $filename = $refDoc;
//           file_put_contents($filename,$nonbinaire);
// Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('content-type','application/pdf');
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

    public function stateAction($hashedRef,$state)
    {
        $ref = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->decrypt($hashedRef);

        if ($ref === false) {
            throw new AuthenticationException('Could not find User');
        }

        $soapService = $this->getSoapService();
        $factures = $soapService->getFacturesByRefClient($ref); // 'C-000000-00033'
        $displayfactures = [];
        foreach($factures as $index => $facture) {
            $factures[$index]['__detail'] = $soapService->getDocument($facture['ref_doc'] );
            if ($factures[$index]['type_doc'] == 4 and $factures[$index]['etat_doc'] == $state)
            {
                $displayfactures[$index] = $factures[$index];
            }
        }



        //var_dump($displayfactures);
        //die();

        return $this->render('StadlineFrontBundle:Default:index.html.twig', array(
            'factures' => $displayfactures
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
}
