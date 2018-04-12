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
 * Date: 3/2/2017
 * Time: 11:57 PM
 */

namespace Gossamer\Horus\Filters\Tests;


use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;

class Filter2 extends AbstractFilter
{

    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain $chain) {

        echo "this is filter2\r\n";
        $chain->execute($request, $response, $chain);
    }

}