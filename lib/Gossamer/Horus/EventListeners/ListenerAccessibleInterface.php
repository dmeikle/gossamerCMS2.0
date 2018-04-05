<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2/28/2017
 * Time: 7:43 PM
 */

namespace Gossamer\Horus\EventListeners;


interface ListenerAccessibleInterface
{

    public function getTablename();
}