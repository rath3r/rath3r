<?php
/**
 * 
 * MySQL Track Data Source
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage MySQL
 * 
 */

class Vanilla_MySQL_Track extends Vanilla_MySQL
{
	/**
	 * Name of the MySQL table we'll be getting the data from
	 * @var string
	 */
	protected $_table = 'tracks';
	
	
	public function getAllByUserId($user_id, $page_size=10, $page_num=1) {
		
		$result = $this->getAll($page_size, $page_num, array('user_id' => $user_id), "date DESC");
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
	
	public function getAllBetweenDates($from = null, $to = null)
	{
	    $insert = array();
	    $sql    = "SELECT * FROM `".$this->_table."`";
	    
	    if($from !== null || $to !== null)
	    {
	        $sql .= " WHERE";
	    }
	    
	    if($from !== null)
	    {
	        $insert[] = " date >= '" . $this->_db_connection->real_escape_string($from)."'";
	    }
	    
	    if($to !== null)
	    {
	        $insert[] = " date <= '" . $this->_db_connection->real_escape_string($to)."'";
	    }
	    
	    $sql .= implode(" AND", $insert);
	     // run query
        $result = $this->query($sql);
        $this->_parseRowset($result);
        return $this;
	}
	
}