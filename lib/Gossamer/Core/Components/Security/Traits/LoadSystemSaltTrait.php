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
 * Date: 8/31/2017
 * Time: 10:22 PM
 */

namespace Gossamer\Core\Components\Security\Traits;



use Gossamer\Caching\Exceptions\FileNotFoundException;
use Gossamer\Essentials\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Essentials\Configuration\SiteParams;
use Gossamer\Essentials\Configuration\YamlLoader;

trait LoadSystemSaltTrait
{

    protected function getSalt(SiteParams $siteParams) {
        $loader = new YamlLoader();
        $loader->setFilePath($siteParams->getConfigPath() . 'config.yml');

        $config = $loader->loadConfig();

        if($config === false) {
            throw new FileNotFoundException('unable to locate ' . $siteParams->getConfigPath() . 'config.yml');
        }
        if(!array_key_exists('system', $config) || !array_key_exists('salt', $config['system'])) {
            throw new KeyNotSetException('salt not configured in ' . $siteParams->getConfigPath() . 'config.yml');
        }
        unset($loader);

        return $config['system']['salt'];
    }
}