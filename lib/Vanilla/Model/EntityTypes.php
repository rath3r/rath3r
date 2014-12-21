<?php

/**
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @name Vanilla_Model_EntityType
 * 
 */

class Vanilla_Model_EntityTypes extends Vanilla_Model_Rowset
{
	/**
	 * Name of the corresponding Row Class
	 * @var string
	 */
	public $row_class_name = "Vanilla_Model_EntityType";
	
	public static $model_cache = array();
	
	
	/**
	 * 
	 * Get Entity Name by Id
	 * @param int $id
	 */
	
	public function getEntityNameById($id)
	{
		$entity_type = Vanilla_Model_EntityType::factory()->getById($id);
		return $entity_type->name;
	}
	
	/**
	 * 
	 * Getting an Object by Type
	 * @param id $type_id
	 * @param boolean $row
	 * @return string Class Name
	 */
	
	public function getObjectByType($type_id, $row = false)
	{
	    if($type_id == 16)
		{
		    return "Surveys_Model_Analysis";
		}
	    
		if(isset(self::$model_cache[$type_id][$row?1:0])) {
			return self::$model_cache[$type_id][$row?1:0];
		}
		
		
		
		$model = $this->getEntityNameById($type_id);
		if($row == true)
		{
			// removing s from the end to make it singular
			// should work for most, but might need special case hardcoded write up
			// or extending the table to hardcode the class types
			
			// if $model contains undescores treat differently
			if (preg_match("/_/", $model)) {
				$model = preg_replace("/ /","",ucwords(preg_replace("/s_/"," ",$model)));
			} else {
				$model = substr($model, 0, strlen($model) - 1);
			}
		}
		
		$model = "Model_". ucwords($model);
		
		self::$model_cache[$type_id][$row?1:0] = $model;
		
		return $model;
	}	
}