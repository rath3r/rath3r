<?php

/**
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @name Vanilla_Model_Track
 * 
 */

class Vanilla_Model_Tracks extends Vanilla_Model_Rowset
{
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Track";
	
	public $row_class_name = "Vanilla_Model_Track";
	
	public function getAllByUserId($user_id, $page_size=10, $page_num=1)
	{
		$db   = $this->getDAOInterface();
		return $db->getAllByUserId($user_id, $page_size, $page_num);
	}

}