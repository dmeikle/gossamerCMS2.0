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
 * Date: 3/8/2017
 * Time: 9:41 PM
 */

namespace Gossamer\Horus\Http;


use Gossamer\Neith\Logging\LoggingInterface;

interface HttpAwareInterface {

    public function setHttpRequest(HttpRequest $request);

    public function setHttpResponse(HttpResponse $response);

    public function setLogger(LoggingInterface $logger);
}