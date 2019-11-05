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
 * Date: 9/17/2017
 * Time: 11:31 AM
 */

namespace Gossamer\Core\Serialization\Filters;


use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;

class PadEntityValuesFilter extends AbstractFilter
{

    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {

        $serializerName = $this->filterConfig->get('serializer');
        $serializer = new $serializerName();

        $data = $this->httpRequest->getAttribute('REQUEST_RESULT_DATA');

        if(!is_array($data)) {
            $data = array();
        }

        if(!array_key_exists($this->filterConfig->get('entity'), $data) || !is_array($data[$this->filterConfig->get('entity')])) {
            $data[$this->filterConfig->get('entity')] = array();
        }

        $serializer->padEntityValues($data[$this->filterConfig->get('entity')]);

        $this->httpRequest->setAttribute('REQUEST_RESULT_DATA', $data);
    }
}