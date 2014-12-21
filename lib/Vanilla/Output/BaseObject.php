<?php

/**
 * Vanilla_Output_BaseObject
 * For all the base functions of Vanilla Output funcitons
 * 
 * @package    Vanilla
 * @subpackage Output
 * @author     Kasia Gogolek <kasia-gogolek@living-group.com>
 * 
 */

abstract class Vanilla_Output_BaseObject {
	
	/**
	 * Stores response values
	 * @var Object
	 */
	public $response;
	
	/**
	 * Stores data values
	 * @var Array
	 */
	public $data;
	
	/**
	 * @magic get method
	 * @param string $var
	 * @return mixed
	 */
	
	public function __get($var)
	{
		return $this->response[$var];
	}
	
	/**
	 * @magic set method
	 * @param string $var
	 * @param mixed $value
	 */
	
	public function __set($var, $value)
	{
		$this->response[$var] = $value;
	}

	/**
	 * Setting the data variable
	 * @param array $data
	 */
	
	public function setData($data)
	{
		$this->data = $data;
	}
	
} 