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
 * Date: 10/31/2017
 * Time: 9:04 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Traits;


use Gossamer\Pesedget\Extensions\Couchbase\Documents\DefaultValuesInterface;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;

/**
 * Class DocumentPreparationTrait
 * @package Gossamer\Pesedget\Extensions\Couchbase\Traits
 *
 * needs to be used in conjunction with bucket trait to have access to bucket connection
 */
trait DocumentPreparationTrait
{
    protected function populateDefaultValues(Document &$document, array $request) {
        if(!$document instanceof DefaultValuesInterface) {
            return;
        }

        foreach($document->getDefaults() as $key => $values) {
            $document->set($key, $values);
        }
    }

    protected function populateDocument(Document &$document, array $request) {

        $namepace = explode('\\', $document->getNamespace());
        $directory = array_shift($namepace);
        $path = $this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $directory .
            DIRECTORY_SEPARATOR . array_shift($namepace) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'schemas.yml';

        $schema = $this->getSchema($document, $path);

        //populate any values that are in the DefaultValuesInterface
        $this->populateDefaultValues($document, $request);

        $document->populate($request, $schema);
    }

    protected function prepare(Document $document, array &$params)
    {
        $this->setDocumentId($document, $params);
        $this->setDocumentType($document, $params);
        $this->setActive($params);
        $this->setUniqueKey($params);
    }

    protected function setUniqueKey(array &$params)
    {
        if (!array_key_exists('uniqueKey', $params)) {
            $params['uniqueKey'] = uniqid();
        }

    }

    protected function setActive(array &$params)
    {
        if (!array_key_exists('isActive', $params)) {
            $params['isActive'] = '1';
        }
    }

    protected function setDocumentType(Document $document, array &$params)
    {
        if (array_key_exists('type', $params)) {
            return;
        }

        $params['type'] = $document->getIdentityField();
    }

    protected function setDocumentId(Document $document, array &$params)
    {
        if (array_key_exists('id', $params) && strlen($params['id']) > 0) {

            return;
        }

        $counter = $this->getBucket()->counter($document->getDocumentKey(), 1, array('initial' => 100));
        $params['id'] = $document->getDocumentKey() . $counter->value;


//        try {
//            // Do not override default name, fail if it is exists already, and wait for completion
//            $this->getConnection()->manager()->createN1qlPrimaryIndex('posts', false, false);
//            echo "Primary index has been created\n";
//        } catch (CouchbaseException $e) {
//            printf("Couldn't create index. Maybe it already exists? (code: %d)\n", $e->getCode());
//        }
//

        //  return $this->getConnection()->counter("posts", 1);
    }
}