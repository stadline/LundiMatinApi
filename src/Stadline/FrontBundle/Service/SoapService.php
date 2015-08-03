<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 11/12/14
 * Time: 11:05
 */

namespace Stadline\FrontBundle\Service;

use SoapClient;
use SoapHeader;
use Stadline\FrontBundle\Entity\SoapHeaderUsernameToken;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SoapService extends ContainerAwareCommand
{
    /** @var SoapClient $client */
    private $client;

    public function __construct($url, $api, $login, $password)
    {
        $usernameToken = new SoapHeaderUsernameToken($login, $password);
        $soapHeaders[] = new SoapHeader('http://schemas.xmlsoap.org/ws/2002/07/utility', 'UsernameToken', $usernameToken);
        $this->client = new SoapClient(null, array(
            'location'      => $url."/api.php?ref_api=".$api,
            'uri'           => "urn:Liaison",
            'trace'         => true,
            'exceptions'    => true
        ));
        $this->client->__setSoapHeaders($soapHeaders);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function soapCall($function, array $args)
    {
        return $this->getClient()->__soapCall($function, $args);
    }

    public function do_argument($datas)
    {
        $stdclass = new stdclass();
        $stdclass->_params = $datas;
        $stdclass->_conversion = array();

        return $stdclass;
    }

//    public function getFacturesByRefClient($refContact, $idCanal)
//    {
//        try {
//            
//            $client = $this->getClient();
//            $response = $client->getFactureByRef($this->do_argument(array(
//                'ref_contact' => $refContact, //  value : C-000000-00033
//                'id_canal' => $idCanal // value : 1
//            )));
//            
//            var_dump($response); //return  null
//            
//            return $response; 
//            
//        } catch (\SoapFault $e) {
//            var_dump($e->getMessage());
//            die('Error');
//            echo $idCanal." : ".$e->getMessage().'<br />';
//            return false;
//        }
//    }
//    
    public function getAllFactures()
    {
        try {

            $client = $this->getClient();
            $response = $client->getList($this->do_argument(array(
            )));

            $datas = json_decode($response['data'], true);
            
            return $datas; 
            
        } catch (\SoapFault $e) {
            return false;
        }
    }


    
    public function getFacturesByRefClient($refClient)
    {
        $allInvoices = $this->getALLFactures();
        
        $collection = new \Doctrine\Common\Collections\ArrayCollection($allInvoices);

        $collection = $collection->filter(function ($elm) use ($refClient) {

            return $elm['ref_contact'] == $refClient;

        });
        
        return array_values($collection->toArray());
    }

    public function getFacturesByRef($refDoc)
    {
        $allInvoices = $this->getAllFactures();

        $collection = new \Doctrine\Common\Collections\ArrayCollection($allInvoices);

        $collection = $collection->filter(function ($elm) use ($refDoc) {

            return $elm['ref_doc'] == $refDoc;
        });

        return array_values($collection->toArray());
    }

    public function getDocument($refDoc)
    {
       try {
            
            $client = $this->getClient();
            $response = $client->getContent($this->do_argument(array(
                'ref_doc' => $refDoc
            )));
            
            $data = json_decode($response['data'], true);
            
        } catch (\SoapFault $e) {
            return false;
        }
        
        return array_pop($data);
    }
    
    public function getPdfDocument($refDoc)
    {
       try {
            
            $client = $this->getClient();
            $response = $client->get_document_pdf($this->do_argument(array(
                'ref_doc' => $refDoc,
                'code_model_pdf' => "doc_fac_standard",
            )));


            $binaire = $response['data']["document"];

           return $binaire;

        } catch (\SoapFault $e) {
            return false;
        }
        
        return $response;
    }


    public function hasFacturesByRefClient($refContact, $idCanal)
    {
        $factures = $this->getFacturesByRefClient($refContact, $idCanal);
        if ($factures === false) return false;
        return isset($factures[0]); // at least one
    }


    public function getRefClientInformation($refClient) {
        try {

            $client = $this->getClient();
            $response = $client->getExtraitCompte($this->do_argument(array(
                'ref_client' => $refClient,
            )));

            $datas = json_decode($response['data'], true);

            return $datas;

        } catch (\SoapFault $e) {
            return false;
        }
    }
} 