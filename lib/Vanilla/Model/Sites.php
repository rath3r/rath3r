<?php

/**
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @name Vanilla_Model_Track
 * 
 */

class Vanilla_Model_Sites extends Vanilla_Model_Rowset
{
	public $row_class_name = "Vanilla_Model_Site";
	
	public function __clone()
	{
	    return unserialize(serialize($this));
	}
	
}