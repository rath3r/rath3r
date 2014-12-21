<?php
/**
 * @name Vanilla_Model_Fund
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Fund extends Vanilla_Model_Row
{
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Fund";
	
	public $required_fields = array('name');
	
	/**
	 * Entity ID for setting up relations
	 * @var int
	 */
	public $entity_id = 4;
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::validate()
	 * @param array $data;
	 * @return boolean
	 */
	public function validate($data)
	{
		if(!Vanilla_Model_Funds::fieldIsUnique('name', $data['name']))
		{
			$this->errors[] = "Fund with the name <i>". $data['name']."</i> already exists. Please change it.";
		}
		return parent::validate($data);
	}
	
	public function addFileGroup($file_group_id)
	{
		$data['parent_id'] = $this->id;
		$data['parent_entity_type_id'] = $this->entity_id;
		$data['child_id']  = $file_group_id;
		$data['child_entity_type_id'] = Model_File_Group::factory()->entity_id;
		$relation = Vanilla_Model_Relationship::factory()->create($data);
		if($relation->id > 0)
		{
			return $relation->id;
		}
		return false;
	}
	
	public function getAllFileGroups()
	{
		$this->filegroups = Vanilla_Model_Relationships::factory()->getAllRelated($this->id, 'funds');
		return $this;
	}
	
	public function removeFileGroup($filegroup_id)
	{
		return Vanilla_Model_Relationship::factory()->delete($this->id, 'funds', $filegroup_id, 'file_group');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::track()
	 * @param int $user_id
	 * @param string $action
	 * @param string $info
	 */
	public function track($user_id, $action, $info = null)
	{
		if($info === null)
		{
			$info = $this->name;
		}
		return parent::track($user_id, $action, $info);
	}
	
}