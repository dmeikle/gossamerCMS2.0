<?php
/**
 * Created by PhpStorm.
 * User: davemeikle
 * Date: 2018-08-23
 * Time: 8:00 PM
 */

namespace Gossamer\Ra\Exceptions;


class UnauthorizedAccessException extends \Exception
{

    public function __construct($message = 'Client access unauthorized', $code = 403, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}