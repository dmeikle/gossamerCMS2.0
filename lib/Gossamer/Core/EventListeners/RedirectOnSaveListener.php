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
 * Date: 9/18/2017
 * Time: 4:02 PM
 */

namespace Gossamer\Core\EventListeners;


use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

class RedirectOnSaveListener extends AbstractListener
{

    public function on_save_success(Event $event) {
        $id = $event->getParam('id');
        $post = $this->request->getRequestParams()->getPost();
        
        if(!array_key_exists('id', $post) && $id != '') {
            
        }
    }
}