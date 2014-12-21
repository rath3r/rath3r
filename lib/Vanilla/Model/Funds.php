<?php

/**
 * @name Vanilla_Model_Funds
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Funds extends Vanilla_Model_Rowset
{
	/**
	 * Name of the corresponding Row Class
	 * @var string
	 */
	public $row_class_name = "Vanilla_Model_Fund";
	
	public function getAllLiveAndFileGroups()
	{
		$funds = self::getAll();
		foreach($funds->rowset as &$fund)
		{
			$fund->getAllFileGroups();
		}
		return $funds;
	}
	
}