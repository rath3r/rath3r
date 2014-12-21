<?php

/**
 * Vanilla Cache
 * Will handle all the cache, is initiated as a singleton
 * 
 * @name     Vanilla_Cache
 * @category Cache
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla Cache
 * Will handle all the cache, is initiated as a singleton
 * 
 * @name     Vanilla_Cache
 * @category Cache
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Cache
{
    /**
     * Singleton Instance
     * @var Vanilla_Cache
     */
    private static $_instance;

    /**
     *  Initiating singleton of Database Connection
     *  
     *  @param boolean $persist Set to true if you want to switch caching on regardless of the global set up
     *  
     *  @return object Database
     */

    public static function getInstance($persist = false, $bum = false)
    {
        if($persist == false && (!defined("CACHE_ENGINE") || !self::cacheIsOn()))
        {
            return null;
        }

        switch(CACHE_ENGINE)
        {
            case "memcache":
                if(class_exists("Memcache"))
                {    
                    return Vanilla_Cache_Memcache::getInstance();
                }
                return null;
            break;
            case "redis":
                return Vanilla_Cache_Redis::getInstance();
            break;
            case "MongoDB":
                return Vanilla_Cache_MongoDB::getInstance();
            break;
            default:
                return null;
                break;
                
        }
    }

    /**
     * Get value for cache key
     * 
     * @param string  $key     Cache key
     * @param boolean $persist Set to true, if you want to overwrite global cache on setting
     * 
     * @return mixed
     */
    public static function get($key)
    {
        $cache = self::getInstance();
        if(is_object($cache))
        {
            $result = $cache->get($key);
            return $result;
        }
        return false;
    }
    
    /**
     * Set Cache key with value
     * 
     * @param string  $key     Unique key name
     * @param mixed   $value   Value we will set
     * @param boolean $persist Set to true, if you want to overwrite global cache on setting
     * 
     * @return mixed
     */
    public static function set($key, $value)
    {
        $cache = self::getInstance();
        if(is_object($cache))
        {
            $result = $cache->set($key, $value);
            return $result;
        }
        return false;
    }
    
    /**
     * Remove all cache from the system
     * 
     * @return void
     */
    public static function flush()
    {
        $cache = self::getInstance(false, true);
        if(is_object($cache))
        {
            Vanilla_Log::log("FLUSH CACHE");
            return $cache->flush();
        }
        return false;
    }
    
    /**
     * Checking if cache is on
     * 
     * @return boolean
     */

    public static function cacheIsOn()
    {
        if (defined('APP_CACHE') && APP_CACHE == "on") {
            return true;
        }
        return false;
    }

}
