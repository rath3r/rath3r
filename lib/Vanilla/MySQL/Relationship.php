<?php

/**
 * 
 * MySQL Page Data Source
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage MySQL
 * 
 */

class Vanilla_MySQL_Relationship extends Vanilla_MySQL
{

	/**
	 * Name of the MySQL table we'll be getting the data from
	 * @var string
	 */
	protected $_table = 'relationships';
	
	public function deleteAllForChild($child_id, $child_type_id, $parent_entity_type_id)
	{
		$sql = " DELETE FROM `". $this->_table ."`"
			 . " WHERE `child_entity_type_id`=".$this->_db_connection->real_escape_string($child_type_id) 
			 . " AND `child_id`=".$this->_db_connection->real_escape_string($child_id)
			 . " AND `parent_entity_type_id`=".$this->_db_connection->real_escape_string($parent_entity_type_id);
			 
		$this->query($sql);
		return true;
	}
	
	/**
	 * 
	 * Get All Related entities
	 * @param int $parent_id
	 * @param string $parent_entity_type
	 * @param string $child_entity_type
	 */
	
	public function getAllRelated($parent_id, $parent_entity_type, $child_entity_type = null, $limit = null, $page = null, $order = null)
	{
		$selectStr = $this->_table .".*, ". $this->_table .".id as relation_id";
		$sql       = $this->_getSql($selectStr, $parent_id, $parent_entity_type, $child_entity_type, $limit, $page);
		$result = $this->query($sql);
		$this->_parseRowset($result);
		return $this->_data;
	}

	/**
	 * 
	 * Get All Related entities backwards. So if you assign an event to a page
	 * Then running this method for an event finds the page
	 * @param int $parent_id
	 * @param string $parent_entity_type
	 * @param string $child_entity_type
	 */
	
	public function getAllRelatedReverse($parent_id, $parent_entity_type, $child_entity_type = null, $limit = null, $page = null, $order = null)
	{
		$selectStr = $this->_table .".*, ". $this->_table .".id as relation_id";
		$sql .= $this->_getSqlReverse($selectStr, $parent_id, $parent_entity_type, $child_entity_type, $limit, $page);
		$result = $this->query($sql);
		
		$this->_parseRowset($result);
		
		$data = $this->_data;

		// need to swap round the data as it will be currently backwards/
		if (!empty($this->_data)) {
			$data = array();
			foreach ($this->_data as $relation_arr) {
				$row = $relation_arr;
				$row['parent_id'] = $relation_arr['child_id'];
				$row['parent_entity_type_id'] = $relation_arr['child_entity_type_id'];
				$row['child_id'] = $relation_arr['parent_id'];
				$row['child_entity_type_id'] = $relation_arr['parent_entity_type_id'];
				$data[] = $row;
			}
		}
		return $data;
	}

	/**
	 * Get count related
	 */
	public function getCountRelated($parent_id, $parent_entity_type, $child_entity_type = null) {
		
		$selectStr = "count(*) AS count";
		
		$sql .= $this->_getSql($selectStr, $parent_id, $parent_entity_type, $child_entity_type);
		
		$result = $this->query($sql);
		$row    = $result->fetch_assoc();
		return $row['count'];		
	}
	
	protected function _getSql($selectStr, $parent_id, $parent_entity_type, $child_entity_type = null, $limit = null, $page = null, $order = null) {
		
		
		$sql = "SELECT ". $selectStr . " FROM `". $this->_table ."`";
		
		$sql .= " LEFT JOIN `entity_types` AS parent_type ON parent_type.`name`='" .$this->_db_connection->real_escape_string($parent_entity_type)."'";

		if(null !== $child_entity_type)
		{
			$sql .= " LEFT JOIN `entity_types` AS child_type ON child_type.`name`='" .$this->_db_connection->real_escape_string($child_entity_type)."'";
		}
		
		$sql .= " WHERE  `". $this->_table ."`.parent_entity_type_id= parent_type.id"
		. " AND  `". $this->_table ."`.parent_id='" .$this->_db_connection->real_escape_string($parent_id)."'";
		
		if(null !== $child_entity_type)
		{
			$sql .= " AND  `". $this->_table ."`.child_entity_type_id=child_type.id";
		}
		
		$sql .= " ORDER BY `". $this->_table ."`.`order`";
		
		// setting up LIMIT
		if(null !== $limit && null !== $page)
		{
			$sql .= " LIMIT ".$limit * ($page - 1) . ",". $limit;
		}		
		
		return $sql;
		
	}
	protected function _getSqlReverse($selectStr, $parent_id, $parent_entity_type, $child_entity_type = null, $limit = null, $page = null, $order = null) {
		
		$sql = "SELECT ". $selectStr . " FROM `". $this->_table ."`";
		
		$sql .= " LEFT JOIN `entity_types` AS parent_type ON parent_type.`name`='" .$this->_db_connection->real_escape_string($parent_entity_type)."'";

		if(null !== $child_entity_type)
		{
			$sql .= " LEFT JOIN `entity_types` AS child_type ON child_type.`name`='" .$this->_db_connection->real_escape_string($child_entity_type)."'";
		}
		
		$sql .= " WHERE  `". $this->_table ."`.child_entity_type_id = parent_type.id"
		. " AND  `". $this->_table ."`.child_id='" .$this->_db_connection->real_escape_string($parent_id)."'";
		
		if(null !== $child_entity_type)
		{
			$sql .= " AND  `". $this->_table ."`.parent_entity_type_id=child_type.id";
		}
		
		$sql .= " ORDER BY `order`";
		
		// setting up LIMIT
		if(null !== $limit && null !== $page)
		{
			$sql .= " LIMIT ".$limit * ($page - 1) . ",". $limit;
		}		
		
		return $sql;
	}	
	
	/**
	 * 
	 * Get All parent entities
	 * @param int $child_id
	 * @param string $child_entity_type
	 */
	
	public function getAllParents($child_id, $child_entity_type, $parent_entity_type = null)
	{
		$sql = "SELECT `{$this->_table}`.* FROM `{$this->_table}` WHERE child_id = '" . $this->_db_connection->real_escape_string($child_id) . "' AND child_entity_type_id = ";
		$sql .= "(SELECT id FROM `entity_types` WHERE  `name` = '" . $this->_db_connection->real_escape_string($child_entity_type) . "')";
		if($parent_entity_type) {
			$sql .= " AND parent_entity_type_id = (SELECT id FROM `entity_types` WHERE  `name` = '" . $this->_db_connection->real_escape_string($parent_entity_type) . "')";
		}
		$sql .= " ORDER BY `order` ASC";
		$result = $this->query($sql);
		$this->_parseRowset($result);
		return $this->_data;
	}
	
	/**
	 * Get all Entity Types available for the
	 * application as specified in relevant table
	 * @return array
	 */
	
	public function getAllEntityTypes()
	{
		$sql = "SELECT * FROM `entity_types`";
		$result = $this->query($sql);
		$this->_parseRowset($result);
		return $this->_data;
	}	
	
	/**
	 * 
	 * Get Entity Type by Id
	 * @param int $id
	 * @return array
	 */
	
	public function getEntityById($id)
	{
		$sql = "SELECT * FROM `entity_types` WHERE id=".$this->_db_connection->real_escape_string($id);
		$result = $this->query($sql);
		$this->_parseRowset($result);
		return $this->_data;
	}	
	

}