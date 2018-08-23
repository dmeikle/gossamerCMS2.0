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
 * Time: 9:59 PM
 */

namespace Gossamer\Core\MVC;


use Gossamer\Core\Components\Preferences\Managers\UserPreferencesManager;
use Gossamer\Core\Components\Preferences\UserPreferences;
use Gossamer\Core\Navigation\Pagination;
use Gossamer\Essentials\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Essentials\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Horus\Filters\FilterEvents;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Core\System\KernelEvents;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Neith\Logging\LoggingInterface;

class MockableController extends AbstractController
{

    use LoadConfigurationTrait;

    public function mockResult($yml_key) {
        $config = $this->httpRequest->getNodeConfig();

        $path = $this->httpRequest->getSiteParams()->getSitePath() .  $config['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mocks.yml';

        $mockConfig = $this->loadConfig($path);

        if(array_key_exists($yml_key, $mockConfig)) {
            return $this->render($mockConfig[$yml_key]);
        }else {
            throw new \Gossamer\Core\Configuration\Exceptions\KeyNotSetException("$yml_key is missing from mocks config");
        }
    }

}