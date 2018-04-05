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
 * Date: 1/6/2017
 * Time: 10:11 AM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Lib;


use Gossamer\Essentials\Configuration\YamlLoader;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;

class DocumentManager
{


    protected $bucket;
    protected $parser;
    protected $sitePath;

    private $documents = array();

    protected $httpRequest = null;

    protected $container;

    public function __construct(\CouchbaseBucket $bucket, YamlLoader $parser, $sitePath)
    {
        $this->bucket = $bucket;
        $this->parser = $parser;
        $this->sitePath = $sitePath;
    }

    public function setHttpRequest($httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    /**
     * @return mixed
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return DocumentManager
     */
    public function setContainer($container) {
        $this->container = $container;
        return $this;
    }
    

    public function getDocument(Document $document, $id)
    {
        $schema = $this->getDocumentSchema($document);
    }

    protected function getDocumentSchema(Document $document) {
        if(!array_key_exists($document->getNamespace() . $document->getClassName(), $this->documents)) {
            $filepath = $this->getFilepath($document);

            $schema = $this->getSchema($document, $filepath);
            $this->documents[$document->getNamespace() . $document->getClassName()] = $schema;
        }

        return $this->documents[$document->getNamespace() . $document->getClassName()];
    }


    protected function getFilepath(Document $document)
    {
        //pop the 'documents' off so we can access a sibling config folder
        $pieces = explode('\\', $document->getNamespace());
        array_pop($pieces);
        $namespacePath = implode(DIRECTORY_SEPARATOR, $pieces);

        return $this->sitePath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $namespacePath . DIRECTORY_SEPARATOR . 'config/schemas.yml';
    }

    protected function getSchema(Document $document, $filepath)
    {
        $this->parser->setFilepath($filepath);
        $config = $this->parser->loadConfig();

        if (!is_array($config)) {
            throw new ConfigurationNotFoundException($filepath . ' not found');
        }
        if (!array_key_exists($document->getIdentityField(), $config)) {
            throw new KeyNotFoundException($document->getIdentityField() . ' not found in configuration');
        }

        return $config[$document->getIdentityField()];
    }


    protected function resultsToArray($results, $shiftArray = false)
    {
        if (!is_object($results)) {
            return array();
        }
        if ($shiftArray) {
            if (isset($results->rows)) {
                return current(json_decode(json_encode($results->rows), TRUE));
            }
            return current(json_decode(json_encode($results->values), TRUE));
        }
        if (isset($results->rows)) {
            return json_decode(json_encode($results->rows), TRUE);
        }
        return json_decode(json_encode($results->value), TRUE);
    }

}