<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Core\Handlers;

use Monolog\Logger;
use Gossamer\Horus\Http\HttpRequest;

/**
 * Base handler to abstract framework 'stuff' away from the developer
 *
 * @author Dave Meikle
 */
abstract class BaseHandler {

    protected $logger = null;
    protected $defaultLocale = null;
    protected $httpRequest = null;

    /**
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function setHttpRequest(HTTPRequest $request) {

        $this->httpRequest = $request;
    }

    /**
     * accessor
     *
     * @param array $locale
     */
    public function setDefaultLocale(array $locale) {
        $this->defaultLocale = $locale;
    }

    /**
     * checks the creation date of a cache file to see if it is expired
     *
     * @param type $filepath
     * @param type $rootFolder
     *
     * @return boolean
     */
    protected function checkFileIsStale($filepath, $rootFolder) {

        if (!file_exists($this->getDestinationFilepath($filepath, $rootFolder))) {
            return true;
        }

        $sourceTime = filemtime($this->getOriginFilePath($filepath, $rootFolder));
        $destinationTime = filemtime($this->getDestinationFilepath($filepath, $rootFolder));

        //check to see if there is a newer file placed on server
        return $sourceTime > $destinationTime;
    }

    /**
     *
     * @param string $filepath
     *
     * @return string
     */
    protected function getOriginFilePath($filepath) {

        return file_exists(__SITE_PATH . '/src' . $filepath) ? __SITE_PATH . '/src' . $filepath : __SITE_PATH . '/app' . $filepath;
    }

    /**
     * returns the location of where to write the cache to
     *
     * @param string $filepath
     * @param string $rootFolder
     *
     * @return string
     */
    protected function getDestinationFilepath($filepath, $rootFolder) {
        $filepath = str_replace('/includes/' . $rootFolder, '', $filepath);

        return __SITE_PATH . '/web/' . $rootFolder . $filepath;
    }

    /**
     * copies a file from 1 location to another
     *
     * @param string $filepath
     * @param string $rootFolder
     *
     * @return string
     */
    protected function copyFile($filepath, $rootFolder) {
        $filepath = trim($filepath);
        $filepathWithFile = str_replace('/includes/' . $rootFolder . '/', '/', $filepath);

        $chunks = explode('/', $filepathWithFile);

        $filename = array_pop($chunks);
        $old_umask = umask(0);
        $parsedFromPath = file_exists(__SITE_PATH . '/src' . $filepath) ? __SITE_PATH . '/src' : __SITE_PATH . '/app';
        $parsedToPath = __SITE_PATH . '/web/' . $rootFolder . implode('/', $chunks);

        @chmod(__SITE_PATH . '/web/' . $rootFolder . '/', 777);

        @mkdir($parsedToPath, 0777, true);
        @chmod(__SITE_PATH . '/web/' . $rootFolder . '/', 0777);
        @umask($old_umask);

        @copy($parsedFromPath . $filepath, $parsedToPath . '/' . $filename);

        @chmod($parsedToPath, 0755);

        return '/web/' . $rootFolder . implode('/', $chunks) . $filename;
    }

    /**
     * copies a file from 1 location to another
     *
     * @param string $filepath
     * @param string $rootFolder
     *
     * @return string
     */
    protected function copyCoreFile($filepath, $rootFolder) {
        $filepath = trim($filepath);
        $filepathWithFile = str_replace('/includes/' . $rootFolder . '/', '/', $filepath);

        $chunks = explode('/', $filepathWithFile);

        $filename = array_pop($chunks);
        $old_umask = umask(0);
        $parsedFromPath = __SITE_PATH . '/src';
        $parsedToPath = __SITE_PATH . '/web/' . $rootFolder . implode('/', $chunks);


        @chmod(__SITE_PATH . '/web/' . $rootFolder . '/', 777);

        @mkdir($parsedToPath, 0777, true);
        @chmod(__SITE_PATH . '/web/' . $rootFolder . '/', 0755);
        umask($old_umask);

        @copy($parsedFromPath . $filepath, $parsedToPath . '/' . $filename);
        @chmod($parsedToPath, 0755);


        return '/web/' . $rootFolder . implode('/', $chunks) . $filename;
    }

    /**
     * finds all occurrences of a tag inside of content
     *
     * @param string $content
     * @param string $tagName
     *
     * @return string
     */
    protected function getOccurrences($content, $tagName) {
        $lastPos = 0;
        $positions = array();
        while (($lastPos = strpos($content, $tagName, $lastPos)) !== false) {
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($tagName);
        }

        return $positions;
    }

    /**
     * @param array $params
     */
    public abstract function handleRequest($params = array());
}
