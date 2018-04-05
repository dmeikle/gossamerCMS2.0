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
 * Date: 3/5/2017
 * Time: 10:29 PM
 */

namespace Gossamer\Core\Views;


use Gossamer\Core\MVC\AbstractView;

class JSONView extends AbstractView
{

    public function render($data = array()) {

        if(!is_null($this->httpResponse->getAttribute('FORM_SECURITY_TOKEN'))) {
            $data['FORM-SECURITY-TOKEN'] = $this->httpResponse->getAttribute('FORM_SECURITY_TOKEN');
            $this->httpResponse->removeAttribute('FORM_SECURITY_TOKEN');
        }

        if(!is_null($this->httpResponse->getAttribute('Authorization'))) {
            $data['JWT'] = $this->httpResponse->getAttribute('Authorization');
            $this->httpResponse->removeAttribute('Authorization');
        }
        $data = array_merge($data, $this->httpResponse->getAttributes());
        $retval = array(
            'headers' => $this->buildHeaders(), //array('Content-Type: application/json'),
            'data' => json_encode($data)
        );

        return $retval;
    }
}