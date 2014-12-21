<?php

/**
 * @name Vanilla_Model_Table
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Table extends Vanilla_Model_Row
{
	const ENTITY_ID = 5;
	
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Table";
	
	
	/**
	 * Entity type for relationships 
	 * @var int
	 */
	public $entity_id = self::ENTITY_ID;
	

	/**
	 * 
	 * Getting All Entity Types That are available
	 * @return array
	 */
	public function createArrayFromColumnsRows($rows,$columns)
	{	
		$column_array =  array();
		$table_array = array();
		
		// pad array columns
		$column_array = array_pad($column_array,(float) $columns,'');
		
		// pad array rows
		$table_array = array_pad($table_array,(float) $rows,$column_array);
		
		return $this->table_array = $table_array;

	}	
		
	
	/**
	 * 
	 * Getting All Entity Types That are available
	 * @return array
	 */
	public function convertPostToArray($data)
	{	
		foreach($data as $key => $value){
			$key_parts = explode('-',$key);
			$row = explode('_',$key_parts[0]);
			$column = explode('_',$key_parts[1]);
			$table_array[$row[1]][$column[1]] = $value;
		}
		$this->table_array = $table_array;
		return $this;
	}	

	/**
	 * 
	 * Update table based on id, data array
	 * @return array
	 */
	public function updateTable()
	{	
		$data = array(
			'data' => base64_encode(serialize($this->table_array))
		);
		return $this->update($data);
	}
	
	/**
	 * 
	 * load table based on id
	 * @return array
	 */
	public function loadTable()
	{	
		$this->table_array = unserialize(base64_decode($this->data));
		return $this;
	}
	
	/**
	 * 
	 * load table based on id
	 * @return array
	 */
	public function getTableData()
	{	
		return $this->table_array;
	}
	
	/**
	 * 
	 * load table based on id
	 * @return array
	 */
	public function addRow()
	{
		$row = $this->table_array[0];
		
		$new_column_array = array();
		$new_column_array = array_pad($new_column_array,count($row),'');
		
		$this->table_array = array_pad($this->table_array,(count($this->table_array)+1),$new_column_array);
		$this->cleanTable();
		return $this;
	}
	
	public function addColumn()
	{
		foreach($this->table_array as &$row){
			$row = array_pad($row,(count($row)+1),'');
		}
		$this->cleanTable();
		return $this;
	}

	public function deleteCheckedRows()
	{
		foreach ($_POST['row_check'] as $val) {
			unset($this->table_array[$val]);
		}
		$this->cleanTable();
		return $this;
	}

	public function deleteCheckedColumns()
	{
		foreach ($_POST['column_check'] as $val) {
			foreach ($this->table_array as &$row) {
				unset($row[$val]);
			}
		}
		$this->cleanTable();
		return $this;
	}
	
	public function cleanTable(){
		$this->table_array = array_values($this->table_array);
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
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::toArray()
	 */
	public function toArray() {
		$this->loadTable();
		return parent::toArray();
	}
	
}