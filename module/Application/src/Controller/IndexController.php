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

    public function poolindexAction()
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

    public function indexAction() {
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));

        $oPool = (object)[
            'type'=>'open-ethereum-pool',
            'api_url_stats'=>'http://ec2-18-184-253-12.eu-central-1.compute.amazonaws.com:8080/apietc/stats'];

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

        $aCoinData = [];
        $sRet = file_get_contents('https://api.coinmarketcap.com/v2/ticker/1321/?convert=USD');
        $oInfo = json_decode($sRet);
        if(is_object($oInfo)) {
            $aCoinData = (object)[
                'max_supply'=>$oInfo->data->max_supply,
                'circulating_supply'=>$oInfo->data->circulating_supply,
                'price'=>$oInfo->data->quotes->USD->price,
                'volume_24h'=>$oInfo->data->quotes->USD->volume_24h,
                'market_cap'=>$oInfo->data->quotes->USD->market_cap,
                'percent_change_24h'=>$oInfo->data->quotes->USD->percent_change_24h,
                'date_last_update'=>date('Y-m-d H:i:s',time()),
            ];
        }

        return [
            'aConfig'=>$aConfig,
            'oPool'=>$oPool,
            'oInfo'=>$obj,
            'aCoinData'=>$aCoinData,
        ];
    }

    public function helpAction() {
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));

        return [
            'aConfig'=>$aConfig,
        ];
    }

    public function minersAction() {
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $json = file_get_contents('http://ec2-18-184-253-12.eu-central-1.compute.amazonaws.com:8080/apietc/miners');
        $obj = json_decode($json);

        return [
            'aConfig'=>$aConfig,
            'aInfo'=>$obj,
        ];
    }

    public function accountAction() {
        $sAccount = $this->params('id');
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $json = file_get_contents('http://ec2-18-184-253-12.eu-central-1.compute.amazonaws.com:8080/apietc/accounts/'.$sAccount);
        $obj = json_decode($json);

        return [
            'aConfig'=>$aConfig,
            'aInfo'=>$obj,
            'sAccount'=>$sAccount,
        ];
    }
}
