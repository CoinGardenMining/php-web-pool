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
    protected $aPools = [];
    protected $oRedis = false;

    public function __construct()
    {
        $this->aPools = [
            'etc' => 'etc-eu1.cgpools.io',
            'ella' => 'ella-eu1.cgpools.io',
        ];
    }

    public function poolindexAction()
    {
        $this->layout('layout/poolindex');

        /**
         * Define List of All Pools
         */
        $aPoolsDB = (object)[
            'etc-eu1' => (object)[
                'api_url_stats'=>'http://etc-eu1.cgpools.io:8080/apietc/stats',
                'type'=>'open-ethereum-pool',
                'pool_coin'=>'ETC',
                'url'=>'http://etc.cgpools.io',
                'name'=>'Ethereum Classic',
                'ccc_id'=>1321,
            ],
            'ella-eu1' => (object)[
                'api_url_stats'=>'http://ella-eu1.cgpools.io:8080/api/stats',
                'type'=>'open-ethereum-pool',
                'pool_coin'=>'ELLA',
                'url'=>'http://ella.cgpools.io',
                'name'=>'Ellaism',
                'ccc_id'=>2122,
            ],
        ];
        $aPools = [];
        $obj = [];

        if(count($aPoolsDB) > 0) {
            foreach($aPoolsDB as $oPool) {
                /**github
                 * Load Pool Information by Type Specific JSON API Call
                 */
                switch($oPool->type) {
                    case 'open-ethereum-pool':
                        $json = file_get_contents($oPool->api_url_stats);
                        $obj = json_decode($json);
                        if(is_object($obj)) {
                            $oPool->dTotalHashrate = $obj->hashrate;
                            $oPool->iTotalMiners = $obj->minersTotal;
                            $oPool->iCurrentBlock = $obj->nodes[0]->height;
                            $oPool->iCurrentDiff = $obj->nodes[0]->difficulty;
                        }
                        break;
                    default:
                        $oPool->dTotalHashrate = 0;
                        $oPool->iTotalMiners = 0;
                        $oPool->iCurrentBlock = 0;
                        $oPool->iCurrentDiff = 0;
                        break;
                }
                $sRet = file_get_contents('https://api.coinmarketcap.com/v2/ticker/'.$oPool->ccc_id.'/?convert=USD');
                $oInfo = json_decode($sRet);
                if(is_object($oInfo)) {
                    $oPool->aCoinData = (object)[
                        'max_supply'=>$oInfo->data->max_supply,
                        'circulating_supply'=>$oInfo->data->circulating_supply,
                        'price'=>$oInfo->data->quotes->USD->price,
                        'volume_24h'=>$oInfo->data->quotes->USD->volume_24h,
                        'market_cap'=>$oInfo->data->quotes->USD->market_cap,
                        'percent_change_24h'=>$oInfo->data->quotes->USD->percent_change_24h,
                        'date_last_update'=>date('Y-m-d H:i:s',time()),
                    ];
                }
                $aPools[] = $oPool;
            }
        }

        return [
            'aPools'=>$aPools,
            'aInfo'=>$obj,
        ];
    }

    public function indexAction() {
        $this->layout()->sActive = 'home';

        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $oPool = (object)[];
        $oBlockInfo = [];
        switch($aConfig->coin) {
            case 'etc':
                $oPool = (object)[
                    'type'=>'open-ethereum-pool',
                    'api_url_stats'=>'http://etc-eu1.cgpools.io:8080/apietc/stats'];
                $json = file_get_contents('http://etc-eu1.cgpools.io:8080/apietc/blocks');
                $oBlockInfo = json_decode($json);
                break;
            case 'ella':
                $oPool = (object)[
                    'type'=>'open-ethereum-pool',
                    'api_url_stats'=>'http://ella-eu1.cgpools.io:8080/api/stats'];
                $json = file_get_contents('http://ella-eu1.cgpools.io:8080/api/blocks');
                $oBlockInfo = json_decode($json);
                break;
            default:
                break;
        }


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
        $sRet = '{}';
        switch($aConfig->coin) {
            case 'etc':
                $sRet = file_get_contents('https://api.coinmarketcap.com/v2/ticker/1321/?convert=USD');
                break;
            case 'ella':
                $sRet = file_get_contents('https://api.coinmarketcap.com/v2/ticker/2122/?convert=USD');
                break;
            default:
                break;
        }
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
            'oBlockInfo'=>$oBlockInfo,
        ];
    }

    public function helpAction() {
        $this->layout()->sActive = 'help';

        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));

        return [
            'aConfig'=>$aConfig,
            'sURL'=>$this->aPools[$aConfig->coin],
        ];
    }

    public function minersAction() {
        $this->layout()->sActive = 'miners';

        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $json = '{}';
        switch($aConfig->coin) {
            case 'etc':
                $json = file_get_contents('http://etc-eu1.cgpools.io:8080/apietc/miners');
                break;
            case 'ella':
                $json = file_get_contents('http://ella-eu1.cgpools.io:8080/api/miners');
                break;
            default:
                break;
        }
        $obj = json_decode($json);

        return [
            'aConfig'=>$aConfig,
            'aInfo'=>$obj,
        ];
    }

    public function accountAction() {
        $this->layout()->sActive = 'miners';

        $sAccount = $this->params('id');
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $json = '{}';
        switch($aConfig->coin) {
            case 'etc':
                $json = file_get_contents('http://etc-eu1.cgpools.io:8080/apietc/accounts/'.$sAccount);
                break;
            case 'ella':
                $json = file_get_contents('http://ella-eu1.cgpools.io:8080/api/accounts/'.$sAccount);
                break;
            default:
                break;
        }
        $obj = json_decode($json);

        $aCoinData = [];
        $sRet = '{}';
        switch($aConfig->coin) {
            case 'etc':
                $sRet = file_get_contents('https://api.coinmarketcap.com/v2/ticker/1321/?convert=USD');
                break;
            case 'ella':
                $sRet = file_get_contents('https://api.coinmarketcap.com/v2/ticker/2122/?convert=USD');
                break;
            default:
                break;
        }
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
            'aInfo'=>$obj,
            'sAccount'=>$sAccount,
            'aCoinData'=>$aCoinData,
            'oPool'=>$this->getPoolInfo(),
        ];
    }

    public function blocksAction() {
        $this->layout()->sActive = 'blocks';

        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $json = '{}';
        switch($aConfig->coin) {
            case 'etc':
                $json = file_get_contents('http://etc-eu1.cgpools.io:8080/apietc/blocks');
                break;
            case 'ella':
                $json = file_get_contents('http://ella-eu1.cgpools.io:8080/api/blocks');
                break;
            default:
                break;
        }
        $obj = json_decode($json);

        return [
            'aConfig'=>$aConfig,
            'aInfo'=>$obj,
        ];
    }

    public function aboutAction() {
        $this->layout()->sActive = 'about';

        return [];
    }

    public function paymentsAction() {
        $this->layout()->sActive = 'payments';

        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $json = '{}';
        switch($aConfig->coin) {
            case 'etc':
                $json = file_get_contents('http://etc-eu1.cgpools.io:8080/apietc/payments');
                break;
            case 'ella':
                $json = file_get_contents('http://ella-eu1.cgpools.io:8080/api/payments');
                break;
            default:
                break;
        }
        $obj = json_decode($json);

        return [
            'aConfig'=>$aConfig,
            'aInfo'=>$obj,
        ];
    }

    private function getPoolInfo() {
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $oPool = (object)[];
        switch($aConfig->coin) {
            case 'etc':
                $oPool = (object)[
                    'type'=>'open-ethereum-pool',
                    'api_url_stats'=>'http://etc-eu1.cgpools.io:8080/apietc/stats'];
                break;
            case 'ella':
                $oPool = (object)[
                    'type'=>'open-ethereum-pool',
                    'api_url_stats'=>'http://ella-eu1.cgpools.io:8080/api/stats'];
                break;
            default:
                break;
        }


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

        return $oPool;
    }
}
