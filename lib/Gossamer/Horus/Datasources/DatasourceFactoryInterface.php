<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2/28/2017
 * Time: 11:01 PM
 */

namespace Gossamer\Horus\Datasources;


use Monolog\Logger;

interface DatasourceFactoryInterface
{
    public function getDatasource($sourceName, Logger $logger);
}