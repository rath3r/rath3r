<?php

/**
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @name Vanilla_Model_Track
 * 
 */

class Vanilla_Model_Track extends Vanilla_Model_Row
{
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Track";

	/**
	 * (non-PHPdoc)
	 * @param array $data
	 * @see Vanilla_Model_Row::create()
	 */
	public function create($data)
	{
		// adding date here, can't be bothered to type it in everywhere else :)
		$data['date'] = date("Y-m-d H:i:s");
		$data['IP']   = $_SERVER['REMOTE_ADDR']; 
		return parent::create($data);
		
	}
}