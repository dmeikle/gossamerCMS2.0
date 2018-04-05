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
 * Date: 3/19/2017
 * Time: 11:28 AM
 */

namespace Gossamer\Core\Components\Preferences;



class UserPreferences {

    private $params = array(
        'DefaultView' => 'tabbed'
    );

    public function __construct(array $params = null) {
        if (!is_null($params)) {
            //we don't know what object this was loaded from (staff, client, user)
            //so we'll ignore the key names
            $element = array_shift($params);
            if (is_array($element)) {
                $item = current($element);
                //only needed for varying views (tabbed, single page, etc..)
                //$this->setViewType($item['viewType']);
                $this->setDefaultLocale($item['defaultLocale']);
                $this->setHomePage($item['homePage']);
            }
        }
    }

    public function setViewType($view) {
        $this->params['DefaultView'] = $view;
    }

    public function getViewType() {
        return $this->params['DefaultView'];
    }

    public function setDefaultLocale($value) {
        $this->params['DefaultLocale'] = $value;
    }

    public function getDefaultLocale() {
        if (!array_key_exists('DefaultLocale', $this->params)) {
            return null;
        }

        return $this->params['DefaultLocale'];
    }

    public function setHomePage($value) {
        $this->params['homePage'] = $value;
    }

    public function getHomePage() {
        return $this->params['homePage'];
    }

    public function setNotificationTypeId($value) {
        $this->params['NotificationTypeId'] = $value;
    }

    public function getNotificationTypeId() {
        return $this->params['NotificationTypeId'];
    }

    public function toArray() {
        return $this->params;
    }

}