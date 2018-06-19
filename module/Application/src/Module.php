<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Rig\Model\RigTable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    // Add this method:
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function($container) {
                    $redis = false;
                    return new Controller\IndexController($redis);
                },
            ],
        ];
    }

    public function onBootstrap(\Zend\Mvc\MvcEvent $e) {
        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $time_start = microtime(true);
        $viewModel->timestart = $time_start;
        $aConfig = json_decode(file_get_contents('/open-ethereum-pool/config.json'));
        $viewModel->sCoin = $aConfig->coin;
        switch($aConfig->coin) {
            case 'ella':
                $viewModel->sCoinLabel = 'Ellaism';
                $viewModel->sExplorerURL = 'https://explorer.ellaism.org';
                $json = file_get_contents('http://ella-eu1.cgpools.io:8080/apietc/blocks');
                $obj = json_decode($json);
                $viewModel->oBlocks = $obj;
                break;
            case 'eth':
                $viewModel->sCoinLabel = 'Ethereum';
                $viewModel->sExplorerURL = 'https://etherscan.io';
                break;
            case 'etc':
                $viewModel->sCoinLabel = 'Ethereum Classic';
                $viewModel->sExplorerURL = 'https://gastracker.io';
                $json = file_get_contents('http://etc-eu1.cgpools.io:8080/apietc/blocks');
                $obj = json_decode($json);
                $viewModel->oBlocks = $obj;
                break;
            default:
                $viewModel->sCoinLabel = 'Unknown';
                $viewModel->sExplorerURL = 'https://gastracker.io';
                break;
        }
    }
}
