<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/7/16
 * Time: 3:38 PM
 */

namespace Stadline\LundiMatinClientBundle\Mock;


interface SoapServiceMockInterface {
    public function getFacturesByRefClient($refClient);
    public function getFacturesByRef($refDoc);
    public function getDocument($refDoc);
} 