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
 * Date: 3/1/2017
 * Time: 9:04 PM
 */

namespace Gossamer\Core\Kernel;


use Gossamer\Core\Http\BoostrapLoader;
use Gossamer\Core\Http\HttpRequest;
use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

class BootstrapListener extends AbstractListener
{

    public function on_bootstrap(Event $event) {

        $request = new HttpRequest(new BoostrapLoader());
        $event->setParam('HttpRequest', $request);
    }
}