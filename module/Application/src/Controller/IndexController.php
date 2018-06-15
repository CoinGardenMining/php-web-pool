<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @author Praesidiarius
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\TableGateway\TableGateway;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        /**
         * Define List of All Pools
         */
        $aPoolsDB = (object)[
            'etc-eu1' => (object)[
                'api_url_stats'=>'http://ec2-18-184-253-12.eu-central-1.compute.amazonaws.com:8080/apietc/stats',
                'type'=>'open-ethereum-pool',
                'pool_coin'=>'ETC',
                'name'=>'ETC Dev Pool',
            ],
            'zec-eu1' => (object)[
                'api_url_stats'=>'http://ec2-18-184-253-12.eu-central-1.compute.amazonaws.com:8080/apietc/stats',
                'type'=>'znomp',
                'pool_coin'=>'ZEC',
                'name'=>'ZEC Dev Pool',
            ],
            'lux-eu1' => (object)[
                'api_url_stats'=>'http://ec2-18-184-253-12.eu-central-1.compute.amazonaws.com:8080/apietc/stats',
                'type'=>'yimp',
                'pool_coin'=>'LUX',
                'name'=>'LUX Dev Pool',
            ],
        ];
        $aPools = [];

        if(count($aPoolsDB) > 0) {
            foreach($aPoolsDB as $oPool) {
                /**github
                 * Load Pool Information by Type Specific JSON API Call
                 */
                switch($oPool->type) {
                    case 'open-ethereum-pool':
                        $json = file_get_contents($oPool->api_url_stats);
                        $obj = json_decode($json);
                        $oPool->dTotalHashrate = $obj->hashrate;
                        $oPool->iTotalMiners = $obj->minersTotal;
                        $oPool->iCurrentBlock = $obj->nodes[0]->height;
                        $oPool->iCurrentDiff = $obj->nodes[0]->difficulty;
                        break;
                    default:
                        $oPool->dTotalHashrate = 0;
                        $oPool->iTotalMiners = 0;
                        $oPool->iCurrentBlock = 0;
                        $oPool->iCurrentDiff = 0;
                        break;
                }
                $aPools[] = $oPool;
            }
        }

        return [
            'aPools'=>$aPools,
        ];
    }
}
