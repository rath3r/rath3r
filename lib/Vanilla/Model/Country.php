<?php

/**
 * @name Vanilla_Model_Country
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Country extends Vanilla_Model_Row
{
	
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Country";
	
	/**
	 * Get Record by Name
	 * @param string $name
	 * @return
	 */
	
	public function getByName($name)
	{
		return $this->getByField('name', strtoupper($name));
	}	
}