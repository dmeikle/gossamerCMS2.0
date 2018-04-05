<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/16/2017
 * Time: 11:57 PM
 */

namespace Gossamer\Core\Datasources;


interface DatasourceInterface
{
    public function setConnection($connection);

}