<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/1/2017
 * Time: 8:25 PM
 */

namespace Gossamer\Horus\Http;


use Gossamer\Horus\Http\Response;

class HttpResponse extends Response
{


    public function getSiteParams() {
        // TODO: Implement getSiteParams() method.
    }

    public function getRequestParams() {
        // TODO: Implement getRequestParams() method.
    }

    public function getHeaders() {
        return $this->headers;
    }
}