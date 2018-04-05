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
 * Time: 11:27 AM
 */

namespace Gossamer\Core\Components\Preferences\Managers;

use Gossamer\Core\Components\Preferences\UserPreferences;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\Managers\CookieManager;


/**
 * Description of UserPreferencesManager
 *
 * @author Dave Meikle
 */
class UserPreferencesManager {

    private $httpRequest = null;

    const COOKIE_NAME = 'user_preferences';

    public function __construct(HttpRequest &$httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    public function getPreferences() {
        $manager = new CookieManager($this->httpRequest);
        $preferences = $manager->getCookie(self::COOKIE_NAME);

        if (is_null($preferences)) {

            return null;
        }

        unset($manager);

        return $this->parseCookie($preferences);
    }

    public function savePreferences(array $preferences) {
        $manager = new CookieManager($this->httpRequest);

        $manager->setCookie(self::COOKIE_NAME, $preferences);
        unset($manager);
    }

    private function parseCookie(array $values) {
        $userPreferences = new UserPreferences();

        $this->setDefaultLocale($userPreferences, $values);
        $this->setNotificationTypes($userPreferences, $values);
        $this->setViewType($userPreferences, $values);

        return $userPreferences;
    }

    private function setViewType(UserPreferences &$userPreferences, array $values) {
        if (!array_key_exists('DefaultView', $values)) {
            return false;
        }
        $userPreferences->setViewType($values['DefaultView']);
    }

    public function setNotificationTypes(UserPreferences &$userPreferences, array $values) {
        if (!array_key_exists('NotificationType', $values)) {
            return null;
        }

        $notificationType = $values['NotificationType'];

        $userPreferences->setNotificationTypeId(intval($notificationType));
    }

    //we are using a cookie - cannot assume it's safe, so let's see what it holds
    private function setDefaultLocale(UserPreferences &$userPreferences, array $values) {
        if (!array_key_exists('DefaultLocale', $values)) {
            return FALSE;
        }
        $preferredLocale = $values['DefaultLocale'];
        $allowableLocales = $this->httpRequest->getAttribute('locales');

        //check to see if the value in the cookie is a valid locale in our list
        foreach ($allowableLocales as $locale) {
            if ($locale['locale'] == $preferredLocale) {
                $userPreferences->setDefaultLocale($preferredLocale);

                return true;
            }
        }

        //locale wasn't located. either the value in the cookie doesn't exist
        //or the cookie is corrupt
        return false;
    }

}