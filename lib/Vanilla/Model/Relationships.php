<?php

/**
 * @name Vanilla_Model_Relationships
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Relationships extends Vanilla_Model_Rowset
{
	/**
	 * Child entity type
	 * @var string
	 */
	public $child_entity_type;
	
	/**
	 * Name of the corresponding Row Class
	 * @var string
	 */
	public $row_class_name = "Vanilla_Model_Relationship";
	
	/**
	 * Getting all Relationships
	 * 
	 * @param int     $parent_id
	 * @param int     $parent_entity_type_id
	 * @param string  $child_entity_type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * @param int     $limit
	 * @param int     $page
	 * @param string  $order
	 * 
	 * @return array
	 */

	public function getAllRelated($parent_id, $parent_entity_type, $child_entity_type = null, $recursive = false, $limit = null, $page = null, $order = null, $return = "object")
	{
		$_tmp = array();
		$db   = $this->getDAOInterface();
		$data = $db->getAllRelated($parent_id, $parent_entity_type, $child_entity_type, $limit, $page, $order);
		if(!empty($data))
		{
			$data = $this->_getIdArrayOfChildren($data);
			foreach($data as $entity_id => $child_ids)
			{
				$_tmp = array_merge($_tmp, $this->_getAllChildObjects($entity_id, $child_ids, $recursive, null, null, $order, $return));
			}
		}
		return $_tmp;
	}
	
	public function getAllRelatedByIds($parent_id, $parent_entity_type_id, $child_entity_type_id, $limit = null)
	{
	    $params = array(
	        'parent_id' => $parent_id,
	        'parent_entity_type_id' => $parent_entity_type_id,
	        'child_entity_type_id' => $child_entity_type_id
	    );
	    
	    return $this->getAll($limit, null, $params);
	}
	
	/**
	 * Getting all Relationships in reverse! So assigning an event to a page
	 * and running this method on the event will find the page
	 * 
	 * @param int     $parent_id
	 * @param int     $parent_entity_type_id
	 * @param string  $child_entity_type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * @param int     $limit
	 * @param int     $page
	 * @param string  $order
	 * 
	 * @return array
	 */

	public function getAllRelatedReverse($parent_id, $parent_entity_type, $child_entity_type = null, $recursive = false, $limit = null, $page = null, $order = null)
	{
		$_tmp = array();
		$db   = $this->getDAOInterface();
		$data = $db->getAllRelatedReverse($parent_id, $parent_entity_type, $child_entity_type, $limit, $page, $order);
		if(!empty($data))
		{
			$data = $this->_getIdArrayOfChildren($data);
			foreach($data as $entity_id => $child_ids)
			{
				$_tmp = array_merge($_tmp, $this->_getAllChildObjects($entity_id, $child_ids, $recursive, null, null, $order));
			}
		}
		return $_tmp;
	}
	
	/**
	 * Getting all Relationships, checking both ways! So if you assign an Event to a Page
	 * this relationship will show up for both the event and the page, and not just the page
	 * 
	 * @param int     $parent_id
	 * @param int     $parent_entity_type_id
	 * @param string  $child_entity_type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * @param int     $limit
	 * @param int     $page
	 * @param string  $order
	 * 
	 * @return array
	 */

	public function getRelatedBothWays($parent_id, $parent_entity_type, $child_entity_type = null, $recursive = false, $limit = null, $page = null, $order = null)
	{
		$_tmp = array();
		$db   = $this->getDAOInterface();
		$data = $db->getAllRelated($parent_id, $parent_entity_type, $child_entity_type, $limit, $page, $order);
		$data_reverse = $db->getAllRelatedReverse($parent_id, $parent_entity_type, $child_entity_type, $limit, $page, $order);

		$data = empty($data) ? array() : $data;
		$data_reverse = empty($data_reverse) ? array() : $data_reverse;
		
		$data = array_merge($data, $data_reverse);
		
		if(!empty($data))
		{
			$data = $this->_getIdArrayOfChildren($data);
			foreach($data as $entity_id => $child_ids)
			{
				$_tmp = array_merge($_tmp, $this->_getAllChildObjects($entity_id, $child_ids, $recursive, null, null, $order));
			}
		}
		return $_tmp;
	}
	
	/**
	 * Get count of related
	 */
	public function getCountRelated($parent_id, $parent_entity_type, $child_entity_type = null) {
		$db_page = $this->getDAOInterface();
		return $db_page->getCountRelated($parent_id, $parent_entity_type, $child_entity_type);
	}
	/**
	 * Getting all Parents of the child
	 * @param int $child_id
	 * @param string $child_entity_type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * @return array
	 */
	
	public function getAllParents($child_id, $child_entity_type, $parent_entity_type = null)
	{
		$_tmp = array();
		$db   = $this->getDAOInterface();
		$data = $db->getAllParents($child_id, $child_entity_type, $parent_entity_type);
		
		if(!empty($data))
		{
			$data = $this->_getIdArrayOfParent($data);
			foreach($data as $entity_id => $parent_ids)
			{
				 $_tmp = $this->_getAllParentObjects($entity_id, $parent_ids, true);
			}
			
		}
		return $_tmp;
	}
	
	/**
	 * Getting the id of parents
	 * @param array $data
	 * @return $data
	 */
	private function _getIdArrayOfParent($data)
	{
		foreach($data as $value)
		{
			$_tmp[$value['parent_entity_type_id']][$value['id']] = $value['parent_id'];
		}
		return $_tmp;
	}
	
		private function _getAllParentObjects($entity_id, $parent_ids, $recursive = false, $limit = null, $page = null , $order = null)
		{
			$object_name = $this->getObjectByType($entity_id, false);
			$objects      = new $object_name();
			$objects->getIdFromArray($limit, $page, $parent_ids, $order);
			if($recursive && isset($objects->rowset))
			{
				foreach($objects->rowset as &$sub_object)
				{
					$sub_object->relation_id        = array_search($sub_object->id, $parent_ids); 
					$sub_object->entity_type        = $this->getEntityById($entity_id);
					
					$sub_object->getRelated(null, $recursive, $order);
				}
			}
			return $objects->toArray();
		}
	
	/**
	 * Getting the id of children
	 * @param array $data
	 * @return $data
	 */
	private function _getIdArrayOfChildren($data)
	{
		foreach($data as $value)
		{
			$_tmp[$value['child_entity_type_id']][$value['id']] = $value['child_id'];
		}
		return $_tmp;
	}
	
		/**
		 * Get All Child Objects
		 * 
		 * @param unknown_type $entity_id
		 * @param unknown_type $child_ids
		 * @param unknown_type $recursive
		 * @param unknown_type $limit
		 * @param unknown_type $page
		 * @param unknown_type $order
		 * 
		 * @todo rename at some point
		 * 
		 * @return array
		 */
	
		private function _getAllChildObjects($entity_id, $child_ids, $recursive = false, $limit = null, $page = null , $order = null, $return = "object")
		{
			$objects_name = $this->getObjectByType($entity_id, false);
			$objects      = new $objects_name();
		    $object_name = $this->getObjectByType($entity_id, true);
		    
		    foreach ($child_ids as $id)
			{
			    $objects->rowset[] = $object_name::factory()->getById($id);
			}
			
			if(isset($objects->rowset))
			{
				foreach($objects->rowset as $key => &$sub_object)
				{
				    // grab only live objects
				    if(isset($sub_object->status) && $sub_object->status == Vanilla_Model_Row::STATUS_LIVE)
				    {
    					//$sub_object->relationship_label = $sub_object->{$field};
    					$sub_object->relation_id        = array_search($sub_object->id, $child_ids); 
    					$sub_object->entity_type        = $this->getEntityById($entity_id);
    					
    					// can only get related if a file group otherwise this
    					// can loop indefinitely if relationships go round in a circle
    					if ($sub_object->entity_type == 'file_group' && $recursive)
    					{
    						$sub_object->getRelated('files', $recursive, $order);
    					}
				    }
				    else 
				    {
				        unset($objects->rowset[$key]);
				    }
				}
			}
			if($return == "array")
			{
			    $objects = $objects->toArray();
			    return $objects;
			}
			
			return $objects->rowset;
		}
	
		/**
		 * 
		 * Getting the whole data for each entity + some extra fields
		 * @param array $value
		 * @param boolean $recursive (not fully recursive, only goes one level down) 
		 * @return array
		 */
	
		private function _parseEntities($value, $parse_parent=false, $recursive = false, $order)
		{
			if($parse_parent)
			{
				$entity_type_id = $value['parent_entity_type_id'];
				$entity_id      = $value['parent_id'];
			} 
			else 
			{
				$entity_type_id = $value['child_entity_type_id'];
				$entity_id      = $value['child_id'];
			}
			$object_name = $this->getObjectByType($entity_type_id, true);
			$object      = new $object_name();
			$object->getById($entity_id);
			$field                      = $object->getRelatedEntityLabelField();
			$object->relationship_label = $object->$field;
			$object->relation_id        = $value['id'];
			$object->entity_type        = $this->getEntityById($entity_type_id);
			if($recursive == true) 
			{
				$object->getRelated(null, false, $order);
			}
			return  $object->toArray();
			
		}

	/**
	 * Get Entity Name by Id
	 * 
	 * @param int $id
	 * 
	 * @return string
	 */
	
	public function getEntityById($id)
	{
		if(!isset($this->child_entity_type[$id]) || null === $this->child_entity_type[$id])
		{
			$db   = $this->getDAOInterface();
			$data = $db->getEntityById($id);
			if($data[0]['name'] == "files_group")
			{
				$data[0]['name'] = "file_group";
			}
			$this->child_entity_type[$id] = $data[0]['name'];
			
		}
		return $this->child_entity_type[$id];
	}
	
	/**
	 * Get Entity array by Id
	 * 
	 * @param int $id
	 * 
	 * @return array
	 */
	
	public function getEntityArrayById($id)
	{
			$db   = $this->getDAOInterface();
			$data = $db->getEntityById($id);
			if($data[0]['name'] == "files_group")
			{
				$data[0]['name'] = "file_group";
			}
		    if($data[0]['name'] == "carousels_item")
			{
				$data[0]['name'] = "carousel_item";
			}
			return $data;
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
		$entity = $this->getEntityArrayById($type_id);
        $model  = $entity[0]['name']; 
		
		// removing s from the end to make it singular
		// should work for most, but might need special case hardcoded write up
		// or extending the table to hardcode the class types
		
		// if $model contains undescores treat differently
		if (preg_match("/_/", $model)) {
			
			$model = preg_replace("/_/"," ", $model);
			$model = preg_replace("/s_/"," ",ucwords($model));
			$model = str_replace(" ", "_", $model);
		} else {
			$model = substr($model, 0, strlen($model) - 1);
		}
		
		if(!$row)
		{
			$model .= "s";
		}
		if(class_exists("Model_". ucwords($model)))
		{
			return "Model_". ucwords($model);
		}
		else 
		{
			return "Model_". ucwords($entity[0]['module']). "_" . ucwords($model);
		}
	}
	
}
