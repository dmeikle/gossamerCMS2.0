<?php



namespace Gossamer\Caching;

/**
 *
 * @author user
 */
interface CachingInterface {
   
    public function saveToCache($key, $params);
    
    public function retrieveFromCache($key);
    
}
