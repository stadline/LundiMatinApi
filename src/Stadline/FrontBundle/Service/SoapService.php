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

class SoapService
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

    public function getFacturesByRefClient($refContact, $idCanal)
    {
        try {
//            return $this->soapCall('getFacturesByRefClient', array(
//                'ref_contact' => $refContact,
//                'id_canal' => $idCanal
//            ));
            return $this->soapCall('getFactureByRef', array(
                'ref' => 'FAC-00139'
            ));
        } catch (\SoapFault $e) {
            echo $idCanal." : ".$e->getMessage().'<br />';
            return false;
        }
    }

    public function hasFacturesByRefClient($refContact, $idCanal)
    {
        $factures = $this->getFacturesByRefClient($refContact, $idCanal);
        if ($factures === false) return false;
        return isset($factures[0]); // at least one
    }
} 