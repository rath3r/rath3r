<?php

/**
 * @name Vanilla_Model_Relationship
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Permission extends Vanilla_Model_Row
{
	/**
	 * Child entity type
	 * @var string
	 */
	public $child_entity_type;
	
	/**
	 * Name of data source table that will provide data
	 * @var string
	 */
	public $_table = 'permissions';
	
	/**
	 * Name of the Data Source Class name
	 * @var string
	 */
	public $db_class_name = "permission";
	
	/**
	 * 
	 * Getting All Entity Types That are available
	 * @return array
	 */
	public function getEntityTypes()
	{
		$db_data = $this->getDAOInterface();
		return $db_data->getAllEntityTypes();
	}
	
	/**
	 * 
	 * Getting All Entity Types That are available
	 * @return array
	 */
	public function toggle($data)
	{	
		$db_data     = $this->getDAOInterface();
		$rowexists   = $db_data->checkPermissions($data);
		if(!$rowexists){
			$this->create($data);
		} else {
			$this->delete($rowexists['id']);
		}
	}	
	
	
}