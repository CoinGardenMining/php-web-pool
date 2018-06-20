<?php
/**
 * Created by PhpStorm.
 * User: Praesidiarius
 * Date: 04.04.2018
 * Time: 18:33
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Db\TableGateway\TableGateway;

class Pooltools extends AbstractHelper
{
    protected $oDbAdapter;

    // Constructor.
    public function __construct($oDbAdapter)
    {
        $this->oDbAdapter = $oDbAdapter;
    }

    function printHashes($iHash) {
        switch($iHash) {
            case $iHash <= 1000:
                return number_format($iHash,2).' H/s';
                break;
            case $iHash <= (1000*1000):
                return number_format($iHash/1000,2).' KH/s';
                break;
            case $iHash <= (1000*1000*1000):
                return number_format($iHash/(1000*1000),2).' MH/s';
                break;
            case $iHash <= (1000*1000*1000*1000):
                return number_format($iHash/(1000*1000*1000),2).' GH/s';
                break;
            default:
                break;
        }
    }

    function printDifficulty($iDiff) {
        return number_format($iDiff/1000000000000,3,'.','\'').' T';
    }
}
