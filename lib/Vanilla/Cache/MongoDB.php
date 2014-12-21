<?php

/**
 * MongoDB Cache
 *
 * @name     Vanilla_Cache_MongoDB
 * @category Cache
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * MongoDB Cache
 *
 * @name     Vanilla_Cache_MongoDB
 * @category Cache
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Cache_MongoDB extends Vanilla_MongoDB
{
    /**
     * Singleton Instance
     * @var Vanilla_Cache
     */
    private static $_instance;
    
    public $_table = "cache";

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
     * Flush all records
     * Removes all entries from all databases
     * 
     * @return void
     */
    public function flush()
    {
        echo "flush all .";
        //return $this->flushAll();    
    }

    public function get($key)
    {
        $params = array('key' => $key);
        $value  = $this->getCollection()->findOne($params);
        $value  = unserialize($value['value']);
        return $value;
    }
    
    public function set($key, $value)
    {
        $value  = serialize($value);
        $params = array('key' => $key, 'value' => $value);
        $this->getCollection()->insert($params);
    }
}