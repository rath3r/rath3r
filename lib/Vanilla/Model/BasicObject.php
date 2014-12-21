<?php
/**
 * @name       Vanilla_Model_BasicObject
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	   Vanilla
 * @subpackage Model
 * @abstract
 * 
 */

abstract class Vanilla_Model_BasicObject
{
	/**
	 * STATUS LIVE ID
	 * @var int
	 */
	const STATUS_LIVE    = 1;
	
	/**
	 * STATUS PENDING ID
	 * @var int
	 */
	const STATUS_PENDING = 2;
	
	/**
	 * STATUS DELETED ID
	 * @var int
	 */
	const STATUS_DELETED = 0;
	
	/**
	 * Creating a Cache Key for object method 
	 * 
	 * @param string $method
	 * @param array $args
	 * 
	 * @return string
	 */
	
	protected function _getCacheKey($method, $args)
	{
		return md5(DB_DATABASE . "-" . APPLICATION_ENVIRONMENT ."-" . get_class($this) . $method . serialize($args));
	}
	
}