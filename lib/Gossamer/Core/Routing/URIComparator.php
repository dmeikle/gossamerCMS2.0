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
 * Time: 5:16 PM
 */

namespace Gossamer\Core\Routing;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Caching\CacheManager;

/**
 * iterates yml configurations for a matching URI pattern
 *
 * @author Dave Meikle
 */
class URIComparator
{

    use \Gossamer\Core\Routing\Traits\FilenameSanitizer;

    protected $cacheManager = null;

    protected $httpRequest;

    public function __construct(CacheManager $cacheManager = null, HttpRequest &$httpRequest) {
        $this->cacheManager = $cacheManager;
        $this->httpRequest = $httpRequest;
    }

    /**
     *
     * @param array $config
     * @param string $uri
     *
     * @return boolean
     */
    public function findPattern($config, $uri) {
        if(substr($uri, 0,1) != '/'){
            $uri = '/' . $uri;
        }

        // $key = $this->retrieveFromCache($uri, true);

//        if (!is_null($key) && $key !== false) {
//
//            return $key;
//        }

        //first, try to see if we can quickly match by direct pattern, no variables
        $index = array_search($uri, array_column($config, 'pattern'));
        if ($index !== false) {
            $tmpNode = (array_slice($config, $index, 1));
            $node = array_shift($tmpNode);
            if(array_key_exists('methods', $node)) {
                $method = $node['methods'];
                if (in_array($this->httpRequest->getRequestParams()->getMethod(), $method)) {
                    $keys = array_keys($config);

                    return array($keys[$index] => $config[$keys[$index]]);
                }else{
                    //else wrong method
                }
            }else{
                $keys = array_keys($config);

                return array($keys[$index] => $config[$keys[$index]]);
            }

        }

        //now try to find by wildcard parameters
        foreach ($config as $outerkey => $grouping) {
            if (!is_array($grouping)) {
                continue;
            }
            if (array_key_exists('methods', $grouping)) {
                $method = current($grouping['methods']);

                if ($method != $this->httpRequest->getRequestParams()->getMethod()) {
                    continue;
                }
            }

            if (array_key_exists('pattern', $grouping)) {

                if ($grouping['pattern'] == $uri) {

                    // $this->saveToCache($uri, array($outerkey => $grouping));
                    return array($outerkey => $grouping);
                }

                if ($this->parseWildCard($uri, $grouping['pattern'])) {
                    //  $this->saveToCache($uri, array($outerkey => $grouping));

                    return array($outerkey => $grouping);
                }
                if ($result = $this->parseKeys($uri, $grouping['pattern'])) {
                    if ($result !== false) {
                        return array($outerkey => $grouping, 'parameters' => $result);
                    }

                }
            }
        }

        return false;
    }

    private function retrieveFromCache($uri) {
        if (!$this->httpRequest->getSiteParams()->getIsCaching()) {

            return false;
        }
        if (is_null($this->cacheManager)) {
            return false;
        }

        return $this->cacheManager->retrieveFromCache('routing/' . $this->sanitizeFilename($uri));
    }


    /**
     * finds a matching pattern with a wildcard in it
     *
     * @param string $uri
     * @param string $pageName
     *
     * @return boolean
     */
    protected function parseWildCard($uri, $pageName) {

        //knock of the trailing parameters at end of URI
        $chunks = explode('?', $uri);
        $trimmedChunks = rtrim($chunks[0], '/');
        //this is based on URI
        $uriPieces = (explode('/', $trimmedChunks));


        //this is based on config file - remove array_filter as it was dropping last chunk
        $pagePieces = (explode('/', $pageName));

        if (count($uriPieces) != count($pagePieces) || count($pagePieces) < 1) {
            return false;
        }


        for ($i = 0; $i < count($uriPieces); $i++) {
            if (array_key_exists($i, $pagePieces)) {
                if ($pagePieces[$i] == '*') {

                    continue;
                }

                if ($pagePieces[$i] != $uriPieces[$i]) {

                    return false;
                }
            }
        }

        return true;
    }

    protected function parseKeys($uri, $pageName) {

        //knock of the trailing parameters at end of URI
        $chunks = explode('?', $uri);
        $trimmedChunks = rtrim($chunks[0], '/');
        //this is based on URI
        $uriPieces = (explode('/', $trimmedChunks));


        //this is based on config file - remove array_filter as it was dropping last chunk
        $pagePieces = (explode('/', $pageName));

        if (count($uriPieces) != count($pagePieces) || count($pagePieces) < 1) {
            return false;
        }

        $retval = array();
        for ($i = 0; $i < count($uriPieces); $i++) {
            if (array_key_exists($i, $pagePieces)) {
                if (substr($pagePieces[$i], 0, 1) == '{') {
                    $retval[preg_replace("/{(.*)}/", "$1", $pagePieces[$i])] = $uriPieces[$i];
                    continue;
                }

                if ($pagePieces[$i] != $uriPieces[$i]) {

                    return false;
                }
            }
        }

        return $retval;
    }

}
