<?php
/**
 * 
 * MongoDB Track Data Source
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage MongoDB
 * 
 */

class Vanilla_MongoDB_Track extends Vanilla_MongoDB
{
	/**
	 * Name of the MongoDB table we'll be getting the data from
	 * @var string
	 */
	protected $_table = 'tracks';
	
	
	public function getAllByUserId($user_id, $page_size=10, $page_num=1)
	{
		$this->getAll($page_size, $page_num, array('user_id' => $user_id), "date DESC");
		
		$entity_types = new Vanilla_Model_EntityTypes();
		
		if(
			(is_array($this->_data))&&(!empty($this->_data))
		){
			foreach($this->_data as $key => $value)
			{
				if($value['entity_types_id'] > 0 && $value['entity_id'] > 0)
				{
					$object_name = $entity_types->getObjectByType($value['entity_types_id'], true);
					$object      = new $object_name();
					$object->getById($value['entity_id']);
					$this->_data[$key]['entity'] = $object->toArray();
					$this->_data[$key]['entity_type'] = $object_name;
				}
			}
		}
		
		return $this->_data;
	}
}