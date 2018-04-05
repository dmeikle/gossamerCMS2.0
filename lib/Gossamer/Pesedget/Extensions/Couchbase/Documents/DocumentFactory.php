<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/** *
 * Author: dave
 * Date: 10/2/2016
 * Time: 9:59 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Documents;


use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;

class DocumentFactory
{

    private $loader = null;
    
    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    public function getSchema(Document $document, $filepath) {

        $this->loader->setFilepath($filepath);
        $config = $this->loader->loadConfig();

        if(!is_array($config)) {
            throw new ConfigurationNotFoundException($filepath . ' not found');
        }
        if(!array_key_exists($document->getIdentityField(), $config)) {
            throw new KeyNotFoundException($document->getIdentityField() . ' not found in configuration');
        }

        return $config[$document->getIdentityField()];
    }

    public function getEntityConfig(Document $document, $filepath) {

        $this->loader->setFilepath($filepath);
        $config = $this->loader->loadConfig();

        if(!is_array($config)) {
            throw new ConfigurationNotFoundException($filepath . ' not found');
        }
        if(!array_key_exists($document->getClassName(), $config)) {
            throw new KeyNotFoundException($document->getClassName() . ' not found in configuration');
        }

        return $config[$document->getClassName()];
    }
}