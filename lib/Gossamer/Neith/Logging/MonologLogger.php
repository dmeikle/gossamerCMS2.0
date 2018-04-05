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
 * Time: 9:54 PM
 */

namespace Gossamer\Neith\Logging;


use Monolog\Logger;

class MonologLogger extends Logger implements LoggingInterface
{
    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addDebug($message, array $context = array()) {
        // TODO: Implement addDebug() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addInfo($message, array $context = array()) {
        // TODO: Implement addInfo() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addNotice($message, array $context = array()) {
        // TODO: Implement addNotice() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addWarning($message, array $context = array()) {
        // TODO: Implement addWarning() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addError($message, array $context = array()) {
        // TODO: Implement addError() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addCritical($message, array $context = array()) {
        // TODO: Implement addCritical() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addAlert($message, array $context = array()) {
        // TODO: Implement addAlert() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function addEmergency($message, array $context = array()) {
        // TODO: Implement addEmergency() method.
    }

}