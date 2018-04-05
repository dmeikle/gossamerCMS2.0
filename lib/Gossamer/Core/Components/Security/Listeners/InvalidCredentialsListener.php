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
 * Date: 2/9/2018
 * Time: 9:57 PM
 */

namespace Gossamer\Core\Components\Security\Listeners;


use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

class InvalidCredentialsListener extends AbstractListener
{

    public function on_invalid_credentials(Event $event = null) {
        $result = array(
            'headers' => $this->buildHeaders(), //array('Content-Type: application/json'),
            'data' => json_encode($event->getParams())
        );

        renderResult($result);
    }


    protected function buildHeaders() {
        $headers = $this->response->getHeaders();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'HTTP/1.1 401 Unauthorized';

        return $headers;
    }
}