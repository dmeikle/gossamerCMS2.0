<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Horus\Filters\Tests;
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/2/2017
 * Time: 11:42 PM
 */
class FilterDispatcherTest extends \tests\BaseTest
{


    public function testFilterRequest() {
        $filterDispatcher = new \Gossamer\Horus\Filters\FilterDispatcher($this->getLogger());
        $request = new \Gossamer\Horus\Http\Request();
        $response = new \Gossamer\Horus\Http\Response();
        $filterDispatcher->setContainer($this->getContainer());
        $filterDispatcher->setFilters($this->getFilters());

        $filterDispatcher->filterRequest($request, $response);
    }


    private function getFilters() {
        return array(
            array(
                'filter' => 'Gossamer\\Horus\\Filters\\Tests\\Filter1',
            ),
            array(
                'filter' => 'Gossamer\\Horus\\Filters\\Tests\\Filter2',
            ),
            array(
                'filter' => 'Gossamer\\Horus\\Filters\\Tests\\Filter3'
            )
        );
    }

}