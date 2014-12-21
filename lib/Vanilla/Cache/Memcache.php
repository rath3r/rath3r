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

class Vanilla_Cache_Memcache extends Memcache
{
    /**
     * Singleton Instance
     * @var Vanilla_Cache
     */
    private static $_instance;

    /**
     * Checking if the cache client was initiated and if we're asking for this setting
     * If we don't want cache, return null
     * 
     * @return mixed
     */

    /**
     *  Initiating singleton of Database Connection
     *  
     *  @return object Database
     */

    public function getInstance()
    {
        if (self::$_instance === null) {
            $c = __CLASS__;
            self::$_instance = new $c;
	        self::$_instance->addServer(CACHE_HOST, CACHE_PORT);
        }

        return self::$_instance;
    }


    /**
     * Checking if cache is on
     * 
     * @return boolean
     */

    public function cacheIsOn()
    {
        if (defined('APP_CACHE') && APP_CACHE == true) {
            return true;
        }
        return false;
    }
    
    /**
     * Using memcache
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return boolean
     */
    public function set($key, $value)
    {
        return parent::set($key, $value, 0, 0);
    }
    
    public function get($key)
    {
        return parent::get($key);
    }

}
