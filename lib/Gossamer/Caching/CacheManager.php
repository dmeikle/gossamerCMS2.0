<?php

namespace Gossamer\Caching;

use Gossamer\Caching\CachingInterface;
//use Gossamer\Horus\Http\HttpRequest;
// //use Gossamer\Horus\Http\HttpResponse;
use Monolog\Logger;
use Gossamer\Caching\Exceptions\FileNotFoundException;
use Gossamer\Caching\Exceptions\IOException;

/**
 * Description of CacheManager
 *
 * @author Dave Meikle
 */
class CacheManager implements CachingInterface
{

    protected $MAX_FILE_LIFESPAN = 1200;
    protected $MAX_WRITE_TIME_ELAPSED = 60;
    protected $logger = null;
    protected $request;

    const LOAD_STATIC_FILE = true;
    const LOAD_PHP_FILE = false;

    public function __construct(Logger $logger = null, array $params = null) {
        $this->logger = $logger;
        if (!is_null($params)) {
            if (array_key_exists('MAX_FILE_LIFESPAN', $params)) {
                $this->MAX_FILE_LIFESPAN = $params['MAX_FILE_LIFESPAN'];
            }
        }
    }

    public function setHttpRequest( $request) {
        $this->request = $request;
    }

    private function getCacheDirectory() {
        if (defined('__CACHE_DIRECTORY')) {
            return __CACHE_DIRECTORY;
        }

        if(!is_null($this->request)) {
            return $this->request->getSiteParams()->getCacheDirectory();
        }

        throw new \Exception('Cache Directory not configured');
    }


    /**
     * since some cached values need to be re-cached if the database updates
     * this method can be called from a save routine's post save
     * event handling
     *
     * @param string $key - the file to remove
     */
    public function invalidateCache($key) {
        if (file_exists($this->getCacheDirectory() . "$key.cache")) {
            unlink($this->getCacheDirectory() . "$key.cache");
        }
    }

    /**
     * retrieves the file from cache
     *
     * @param string $key
     * @param boolean $static - is it HTML or PHP to execute?
     *
     * @return boolean
     */
    public function retrieveFromCache($key, $static = false) {
       
        //in case the developer has added a subfolder, we need to know this
        $path = $this->buildCompletePath($this->getCacheDirectory(), $key);
        $key = $this->parseKey($key);

        if (file_exists($path . "$key.cache") && $this->isNotStale($path . "$key.cache", $this->MAX_FILE_LIFESPAN)) {
            if (!$static) {
                $loadedValues = include $path . "$key.cache";

                return $loadedValues;
            }

            return file_get_contents($path . "$key.cache");
        }

        return false;
    }

    /**
     * determines whether a file has outlived its decay time
     *
     * @param string $filepath
     * @param string $decayTime
     *
     * @return boolean
     */
    private function isNotStale($filepath, $decayTime) {
         if(!file_exists($filepath)) {
            return false;
        }
        $filetime = filemtime($filepath);
        $currentTime = time();

        return ($currentTime - $filetime) < $decayTime;
    }

    /**
     * builds the filepath for caching files
     *
     * @param string $path
     * @param string $key
     *
     * @return string
     */
    private function buildCompletePath($path, $key) {
        $pieces = explode(DIRECTORY_SEPARATOR, $key);
        if (count($pieces) == 1) {
            return $path;
        }

        array_pop($pieces);

        return $path . implode(DIRECTORY_SEPARATOR, $pieces) . DIRECTORY_SEPARATOR;
    }

    /**
     * gets the key for cache file names
     *
     * @param string $key
     *
     * @return string
     */
    private function parseKey($key) {
        $pieces = explode(DIRECTORY_SEPARATOR, $key);
        if (count($pieces) == 1) {
            return $key;
        }


        return array_pop($pieces);
    }

    /**
     * deletes the cache file
     *
     * @param string $key
     */
    public function deleteCache($key) {
        if (file_exists($this->getCacheDirectory() . "$key.cache")) {
            unlink($this->getCacheDirectory() . "$key.cache");
        }
    }

    /**
     * saves the file to cache directory
     *
     * @param string $key
     * @param string|array $values
     * @param boolean $static
     *
     * @return boolean
     */
    public function saveToCache($key, $values, $static = false) {
        //in case the developer has added a subfolder, we need to know this
        $path = $this->buildCompletePath($this->getCacheDirectory(), $key);
        $key = $this->parseKey($key);

        if ($this->inDogpileMode($key)) {
            //check to see if we're in a stale write condition
            if ($this->isNotStale($path . "$key.cache.dogpile", $this->MAX_WRITE_TIME_ELAPSED)) {
                //someone is already writing to the file so we cannot cache right now
                return;
            }
            //seems to be stale - shouldn't have taken this long to create
        }

        try {
            $this->verifyPathExists($path);

            //first save current cache to dogpile file
            $this->createDogpileFile($key);

            if (!is_null($this->logger)) {
                //$this->logger->addDebug('Caching - saving values to cache file');
            }

            $file = fopen($path . "$key.cache", "w") or die("Unable to write to cache file to $path");
        } catch (\Exception $e) {
            if (!is_null($this->logger)) {
                // $this->logger->addError($e->getMessage());
            }

            return false;
        }

        if ($static) {
            @fwrite($file, $values);
        } else {
            @fwrite($file, $this->formatValuesBeforeSaving($values));
        }

        fclose($file);

        $this->deleteDogpileFile($key);

        return true;
    }

    /**
     * checks to make sure the filepath exists
     *
     * @param string $path
     *
     * @return boolean
     */
    protected function verifyPathExists($path) {
        if (file_exists($path)) {
            return;
        }

        $this->mkdir($path);
    }

    /**
     * creates a temp file for other processes to access (as cache) while the
     * new cache file is being generated and written to
     *
     * @param string $key
     *
     * @return void
     */
    protected function createDogpileFile($key) {
        if (!is_null($this->logger)) {
            // $this->logger->addDebug('Caching - creating shunt for dogpile condition');
        }

        if (!file_exists($this->getCacheDirectory() . "$key.cache")) {
            touch($this->getCacheDirectory() . "$key.cache.dogpile");

            return;
        }

        $this->copy($this->getCacheDirectory() . "$key.cache", $this->getCacheDirectory() . "$key.cache.dogpile");
    }

    /**
     * removes the dogpile file once the cache writing is complete
     *
     * @param string $key
     */
    protected function deleteDogpileFile($key) {
        if (!is_null($this->logger)) {
            // $this->logger->addDebug('Caching - deleting shunt for dogpile condition');
        }
        if (file_exists($this->getCacheDirectory() . "$key.cache.dogpile")) {
            @unlink($this->getCacheDirectory() . "$key.cache.dogpile");
        }
    }

    /**
     * lets us know if we are currently writing to a cache file, so other
     * processes know to use the dogpile file
     *
     * @param string $key
     *
     * @return boolean
     */
    protected function inDogpileMode($key) {
        if (!is_null($this->logger)) {
            //$this->logger->addDebug('Caching - checking for dogpile condition');
        }

        if (file_exists($this->getCacheDirectory() . "$key.cache.dogpile")) {
            if (!is_null($this->logger)) {
                // $this->logger->addDebug('Caching - currently in dogpile condition');
            }
        }

        return file_exists($this->getCacheDirectory() . "$key.cache.dogpile");
    }

    /**
     * formats values before writing to cache
     *
     * @param string|array $values
     *
     * @return string|array
     */
    private function formatValuesBeforeSaving($values) {
        if (!is_null($this->logger)) {
            //$this->logger->addDebug('Caching - formatting values before saving');
        }


        if (is_array($values)) {
            return "<?php\r\n"
            . "return " . $this->parseArray($values) . ";";
        }

        return $values;
    }

    /**
     * parses each element to make an 'array' into a writable string
     *
     * @param array $values
     *
     * @return string
     */
    private function parseArray(array $values) {
        if (!is_null($this->logger)) {
            // $this->logger->addDebug('Caching - parsing array values');
        }

        $retval = "array (";
        $elements = '';
        foreach ($values as $key => $row) {
            if (is_array($row)) {
                $elements .= ",\r\n'$key' => " . $this->parseArray($row);
            } else {
                $elements .= ",\r\n'$key' => '$row'";
            }
        }
        $retval .= substr($elements, 1) . ")";

        return $retval;
    }

    /**
     * Copies a file.
     *
     * This method only copies the file if the origin file is newer than the target file.
     *
     * By default, if the target already exists, it is not overridden.
     *
     * @param string $originFile The original filename
     * @param string $targetFile The target filename
     * @param bool $override Whether to override an existing file or not
     *
     * @throws FileNotFoundException    When originFile doesn't exist
     * @throws IOException              When copy fails
     */
    public function copy($originFile, $targetFile, $override = false) {
        if (stream_is_local($originFile) && !is_file($originFile)) {
            throw new FileNotFoundException(sprintf('Failed to copy "%s" because file does not exist.', $originFile), 0, null);
        }

        $this->mkdir(dirname($targetFile));

        if (!$override && is_file($targetFile) && null === parse_url($originFile, PHP_URL_HOST)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        } else {
            $doCopy = true;
        }

        if ($doCopy) {

            $source = fopen($originFile, 'r');
            // Stream context created to allow files overwrite when using FTP stream wrapper - disabled by default
            $target = fopen($targetFile, 'w', null, stream_context_create(array('ftp' => array('overwrite' => true))));
            @stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);

            if (!is_file($targetFile)) {
                throw new IOException(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), 0, null);
            }
        }
    }

    /**
     * Creates a directory recursively.
     *
     * @param string|array|\Traversable $dirs The directory path
     * @param int $mode The directory mode
     *
     * @throws IOException On any directory creation failure
     */
    public function mkdir($dirs, $mode = 0777) {
        foreach ($this->toIterator($dirs) as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (true !== @mkdir($dir, $mode, true)) {
                $error = error_get_last();
                if (!is_dir($dir)) {
                    // The directory was not created by a concurrent process. Let's throw an exception with a developer friendly error message if we have one
                    if ($error) {
                        throw new IOException(sprintf('Failed to create "%s": %s.', $dir, $error['message']), 0, null);
                    }
                    throw new IOException(sprintf('Failed to create "%s"', $dir), 0, null);
                }
            }
        }
    }

    /**
     * @param mixed $files
     *
     * @return \Traversable
     */
    private function toIterator($files) {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : array($files));
        }

        return $files;
    }

}
