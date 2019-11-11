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
 * Time: 8:34 PM
 */

namespace Gossamer\Core\Kernel;


use Detection\MobileDetect;
use Gossamer\Pesedget\Entities\EntityManager;

class BootstrapLoader
{

    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;


    /**
     * initialize method type based on HTTP_METHOD
     *
     * @param void
     *
     * @return void
     */
    private function initMethod() {

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                return 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                return 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        return $method;
    }

    public function getRequestParams() {
        $requestParams = new \Gossamer\Horus\Http\RequestParams();

        $requestParams->setHeaders(getallheaders());
        $requestParams->setPost($this->convertJson($this->getPost()));
        $requestParams->setQuerystring($this->getFormattedParameterNames());
        $requestParams->setServer($_SERVER);
        $requestParams->setLayoutType($this->getLayoutType());
        $requestParams->setMethod($this->initMethod());
        $requestParams->setSiteURL($this->getSiteURL());

        // $requestParams->setUri()
        return $requestParams;
    }

    /**
     * Because of the nature of rest calls variables with a '.' (eg:BillingAddress.email) need
     * to be converted to '_dot_'. This is where we convert them back to usable format.
     *
     * @return array
     */
    private function getFormattedParameterNames() {
        $retval = array();
        foreach($_GET as $key => $value) {
            if(strpos($key, '_dot_') !== false) {
                $retval[str_replace('_dot_', '.', $key)] = $value;
            }else{
                $retval[$key] = $value;
            }
        }

        return $retval;
    }
    /**
     * @return mixed
     *
     * depending on the request framework, we will find it in the post or php contents (angular does this)
     */
    private function getPost() {
        if (count($_POST) > 0) {
            return $_POST;
        }

        return $this->getRequestContents();
    }

    private function getRequestContents() {

        return json_decode(file_get_contents("php://input"), true);
    }

    /**
     * determines if we are dealing with a computer or mobile device
     *
     * @return array
     */
    private function getLayoutType() {
        $detector = new MobileDetect();
        $isMobile = $detector->isMobile();
        $isTablet = $detector->isTablet();
        unset($detector);

        return array('isMobile' => $isMobile, 'isTablet' => $isTablet, 'isDesktop' => (!$isMobile && !$isTablet));
    }

    public function getEntityManager($configPath) {
        $config = $this->loadConfig($configPath . 'credentials.yml');

        return new EntityManager($config);
    }

    private function getSiteURL() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';

        return $protocol . $domainName;
    }


    private function convertJson(array $parameters = null) {
        if (is_null($parameters)) {
            return array();
        }
        if (current($parameters) == '') {
            array_shift($parameters);
        }

        $retval = array();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            return $parameters;
        }

        foreach ($parameters as $key => $value) {

            if (is_array($value)) {
                $retval[$key] = $this->convertJson($value, true);
            } else {
                if ($this->checkIsJson($value)) {
                    $retval[$key] = (array)json_decode($value, true);
                } else {

                    $retval[$key] = str_replace("'", '`', urldecode($value));
                }
            }
        }

        return $retval;
    }


    private function checkIsJson($string) {
        if (strpos($string, ':') === false) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}