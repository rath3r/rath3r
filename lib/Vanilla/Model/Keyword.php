<?php
/**
 * @name Vanilla_Model_Keyword
 * @author Niall St John <niall.stjohn@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */
class Vanilla_Model_Keyword extends Vanilla_Model_Row
{
	
	const ENTITY_ID = 9;
	
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Keyword";
	
	/**
	 * Entity ID for setting up relations
	 * @var int
	 */
	public $entity_id = self::ENTITY_ID;
	
	/**
	 * List of required fields that will be checked during validation
	 * @var array
	 */
	public $required_fields = array ('name');
	
	public function getAllFileGroups()
	{
		$this->filegroups = Vanilla_Model_Relationships::factory()->getAllRelated($this->id, 'keywords', 'files_group');
		return $this;
	}
	
	
	public function getAllTables()
	{
		$this->tables = Vanilla_Model_Relationships::factory()->getAllRelated($this->id, 'keywords', 'tables');
		return $this;
	}

	/**
	 * 
	 * Relates an entity to a keyword
	 * 
	 * @param int $entity_id
	 * @param int $entity_type_id
	 */
	public function addRelationship($entity_id, $entity_type_id)
	{
		$data['parent_id'] = $this->id;
		$data['parent_entity_type_id'] = $this->entity_id;
		$data['child_id']  = $entity_id;
		$data['child_entity_type_id'] = $entity_type_id;
		$relation = Vanilla_Model_Relationship::factory()->create($data);
		if($relation->id > 0)
		{
			return $relation->id;
		}
		return false;
	}	
	
}