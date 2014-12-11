<?php

namespace Stadline\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class DefaultController extends Controller
{
    public function hashAction($refContact)
    {
        $salt = $this->container->getParameter('secret');
        $allowCreate = $this->getSoapService()->hasFacturesByRefClient($refContact, 1);
        $hashedRef = $this->getDoctrine()->getManager()->getRepository('StadlineFrontBundle:Contact')->encrypt($refContact, $salt, $allowCreate);
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

//        $soapService = $this->getSoapService();
//        $factures = $soapService->getFacturesByRefClient($ref, 1); // 'C-000000-00033'
//
//        echo '<pre>';var_dump($factures);die();

        return $this->render('StadlineFrontBundle:Default:index.html.twig', array(
            'factures' => $this->facturesTest
        ));
    }

    private function getSoapService()
    {
        return $this->get('stadline_front.soap_service');
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
