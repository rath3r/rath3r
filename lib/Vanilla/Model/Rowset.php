<?php
/**
 * 
 * Rowset is a Collection of rows
 * @name Vanilla_Model_Rowset
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @abstract
 *
 */

abstract class Vanilla_Model_Rowset extends Vanilla_Model_BasicObject
{
	/**
	 * Collection Array here
	 * @var array
	 */
	public $rowset;
	
	/**
	 * Name of the corresponding Row Class
	 * @var string
	 */
	public $row_class_name = "Vanilla_Model_Row";
	
	public $_query_params;
	
	public $_query_limit;
	
	public $_query_page;
        
        public $_query_count;
	
	public $joins = array();
	
	/**
	 * Factory new object
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Model_Rowset
	 */
	public static function factory()
	{
		$class = get_called_class();
		$object = new $class();
		return $object;
	}
	
	/**
	 * Get an array of all primary id's in the collection
	 * 
	 * @return array
	 */
	public function getIdArray()
	{
	    if(empty($this->rowset))
	    {
	        return array();
	    }
	    
	    foreach($this->rowset as $key => $row)
	    {
	        $data[] = $row->id;
	    }
	    return $data;
	}
	
	/**
	 * Assign data source results to the class
	 * 
	 * @param Vanilla_Interface_DataSource $db_record DB Record
	 * 
	 * @return Vanilla_Model_Rowset
	 */
	
	public function assign($db_record)
	{
	    $this->rowset = array();
            $db_data      = $db_record->getData();
            if(!empty($db_data))
            {
                foreach($db_data as $rowset)
                {
                    $this->rowset[] = $this->getRowObject()->assign($rowset);
                    $this->_query_count = $db_record->getQueryCount();
                }
            }
            else
            {
                    //@todo throw error;

            }
		
		
		return $this;
	}
	
	/**
	 * Translate into language
	 * 
	 * @param Languages_Model_Language $language Language Object or null if none set
	 * 
	 * @return Vanilla_Model_Rowset
	 */
	public function translate($language = null, $order = null)
	{
	    if(Vanilla_Module::isInstalled("Languages") && $language instanceof Languages_Model_Language)
	    {
    	    if(!empty($this->rowset))
    	    {
        	    foreach($this->rowset as $row)
        	    {
        	        $row->translate($language);
        	    }
        	    if($order !== null)
        	    {
        	        $this->orderBy($order, $language);
        	    }
    	    }
	    }
	    return $this;    
	}
	
	public function orderBy($order, $language)
	{
	    $_tmp     = array();
    	$sorted   = $this->getAssocArray($order);
	    if($language instanceof Languages_Model_Language)
	    {
    	    $locale = new Vanilla_Locale($language->iso . "_" . $language->iso);
            setlocale(LC_COLLATE, $locale->getISO().".UTF-8");
            
    	    usort($sorted, 'strcoll');
	    }	    
	    else
	    {
	       natsort($sorted);
	    }
	    
	    foreach($sorted as $value)
	    {
            foreach($this->rowset as $row)
            {
                if($row->$order == $value)
                {
                    $_tmp[] = $row;
                }
            }
	    }
	    
	    $this->rowset = $_tmp;
	}
	
	/**
	 * Get id from array
	 * 
	 * @param int    $limit Limit
	 * @param int    $page  Page
	 * @param array  $ids   Ids array
	 * @param string $order Order
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Model_Rowset
	 */
	public function getIdFromArray($limit, $page, $ids, $order = null)
	{
		$table      = $this->getDAOInterface();
		$table_name = $table->getTableName();
		
		if($order == null)
		{
			$table->getIdFromArray($limit, $page, $ids);
			$this->assign($table);
		}
		else 
		{
                    if(is_array($order) && isset($order[$table_name]))
                    {
                            $order = $order[$table_name];
                    }
                    else 
                    {
                            $order = null;
                    }

                    $this->getAll($limit, $page, array('id' => array('value' => $ids)), $order);
		}
		
		return $this;
	}
	
	/**
	 * Get all live and grouped.
	 * Grabs all live entities, that don't have a pending change
	 * or those changes
	 * 
	 * @param array $params Parameters Array
	 * 
	 * @return Vanilla_Model_Rowset
	 */
	public function getAllLiveAndGrouped($params = null, $limit = null, $page = null, $order = null)
	{
            $this->setQueryParams($limit, $page, $params);
	    
	    $args    = func_get_args();
		$key     = $this->_getCacheKey(__FUNCTION__, $args);
		$db_data = Vanilla_Cache::get($key);
        
		if(false === $db_data)
		{
			$db_data = $this->getDAOInterface();
                        
			$db_data->getAllLiveAndGrouped($params, $limit, $page, $order);
			Vanilla_Cache::set($key, $db_data);
		}
		$this->assign($db_data);
		return $this;
	}
        
        public function setQueryParams($limit = null, $page = null, $params = null)
        {
            $this->_query_limit  = $limit;
	    $this->_query_page   = $page;
	    $this->_query_params = $params;
        }
	
	/**
	 * Get Row Object from the Rowset
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Model_Row
	 */
	public function getRowObject()
	{
		$class_name     = $this->row_class_name;
		
		//@todo if none set just remove 's from the end
		if($class_name == "Vanilla_Model_Row")
		{
			
		}
		$row            = new $class_name();
		return $row;
	}
	
	/**
	 * Change order of objects
	 * 
	 * @param int    $id        Id of object
	 * @param int    $parent_id Parent Id
	 * @param int    $order     Order number
	 * @param string $direction Direction of ordering
	 * 
	 * @return void
	 */
	
	public function changeOrder($id, $parent_id, $parent_label, $order, $direction)
	{
		$data_source = $this->getDAOInterface();
		$data_source->changeOrder($id, $parent_id, $parent_label, $order, $direction);
		return;
		
	}
	
	/**
	 * Get the total number of results found in the previous query
	 * Make sure this is run directly after the query you're after
	 * @return int
	 */
	public function getFoundRows()
	{
		$data_source = $this->getDAOInterface();
		$data_source->getFoundRows();
		$data = end($data_source->getData());
		return $data['total_count'];
	}
	
	/**
	 * Turns the object into an array of values
	 * @return array
	 */
	
	public function toArray()
	{
		$data = array();
		if(isset($this->rowset))
		{
			foreach($this->rowset as $row)
			{
				$data[] = $row->toArray();
			}
		}
		return $data;
	}
	
	/**
	 * Join a table
	 * @param string $table_name
	 * @param string $on
	 */
	public function join($table_name, $on, $on2 = 'id')
	{
		$this->joins[] = array('table' => $table_name, "on" => $on, "on2" => $on2);
		return $this;
	}
	
	/**
	 * Delete row by row
	 * @return Vanilla_Model_Rowset
	 */
	public function delete()
	{
		if(!empty($this->rowset))
		{
			foreach($this->rowset as $row)
			{
				$row->delete();
			}
		}
		$this->rowset = array();
	}
	

	/**
	 * Delete where
	 * 
	 * @return boolean
	 */
	public function deleteWhere($params)
	{
		$db_obj = $this->getDAOInterface();
		Vanilla_Cache::flush();
		return $db_obj->deleteWhere($params);
	}
	
	public function getQueryLimit()
	{
	    return (int) $this->_query_limit;
	}
	
    public function getQueryPage()
	{
	    return (int) $this->_query_page;
	}
	
	/**
	 * 
	 * Get All records as per parameters below
	 * $params is an array that will be used in the WHERE statement, so put (status => 1) to make sure that only
	 * records with status = 1 are being fetched
	 * 
	 * @param int    $limit    Limit of records grabbed
	 * @param int    $page     Which iteration of limit are we grabbing
	 * @param array  $params   Array of paramteres for WHERE statement
	 * @param string $order    Order
	 * @param string $group_by Group by string
	 * @param string $columns  Columns to be fetched "*" as default
	 * @param string $having   Having string
	 * @param string $not_in   Not in
	 * 
	 * @return Vanilla_Model_Rowset
	 */
	
	public function getAll($limit = null, $page = null, $params = null, $order = null, $group_by = null, $columns = "*", $having = null, $not_in = null)
	{
	    $limit = ($limit == 'ALL')? null : $limit;

	    $this->_query_limit  = $limit;
	    $this->_query_page   = $page;
	    $this->_query_params = $params; 
	    
		$args    = func_get_args();
		$key     = $this->_getCacheKey(__FUNCTION__, $args);
		
		$db_data = Vanilla_Cache::get($key);
		
		if(false === $db_data)
		{
			$db_data = $this->getDAOInterface();
			$db_data->getAll($limit, $page, $params,  $order, $group_by, $columns,  $this->joins, $having, $not_in);
			Vanilla_Cache::set($key, $db_data);
		}
		else 
		{
		    //echo "Has cache<br/>";
		}
		
		$this->assign($db_data);
		return $this;
	}
	/**
	 * Magic method for catching all sorts of activity and passing it to Data Source Class
	 * @param string $method
	 * @param array $args
	 * @return Vanilla_Model_Rowset $this
	 */
	public function __call($method, $args)
	{
		$class_name = get_called_class();
		$key     = $this->_getCacheKey($class_name, $method, $args);
		$db_data = Vanilla_Cache::get($key);
		
		if(false === $db_data)
		{
			$db_data     = $this->getDAOInterface();
			call_user_func_array(array($db_data, $method), $args);
			Vanilla_Cache::set($key, $db_data);
		}
		$this->assign($db_data);
		return $this;
	}
	
	/**
	 * Quick function for getting all live objects
	 * @param int $limit
	 * @param int $per_page
	 * @param string $order
	 * @return Vanilla_Model_Rowset
	 */
	public function getAllLive($limit = null, $per_page = null, $order = null)
	{
            return $this->getAll($limit, $per_page, array('status' => array('value' => Vanilla_Model_Row::STATUS_DELETED, 'operator' => '!=')), $order);
	}
	
	/**
	 * Get count of all elements
	 * @param array $params
	 * @return int
	 */
	
	public function getCount($params = null)
	{
            if($this->_query_count === null)
            {
                $db_page = $this->getDAOInterface();
                if($params === null)
                {
                    $params = $this->_query_params;
                }
                $this->_query_count = $db_page->getCount($params);
            }
            return $this->_query_count;
	}
	
	/**
	 * 
	 * Searching for relationship records
	 * @chainable
	 * @param string $query
	 * @param int $limit
	 * @return Vanilla_Model_Rowset
	 */

	public function findForRelationship($query, $limit = 10)
	{
		$db_page = $this->getDAOInterface();
		$db_page->findForRelationship($query, $limit);
		$this->assign($db_page);
		return $this;
	}
	
	/**
	 * 
	 * Find in the returned rowset
	 * @param string $field_name
	 * @param string $field_value
	 * @return Vanilla_Model_Row
	 */
	
	public function find($field_name, $field_value)
	{
		if(!isset($this->rowset)) return null;
		foreach($this->rowset as $row)
		{
			if(strtoupper($row->$field_name) == strtoupper($field_value))
			{
				return $row;
			}
		}
		return null;
	}
	
	public function getDataSourceClassObject()
	{
	    return $this->getDAOInterface();
	}
	
	/**
	 * Getting the Data Source Object
	 * All the functionality should be the same as in Row
	 * So just calling that object and initiating the class
	 * @return string
	 */
	
	public function getDAOInterface()
	{
		$class_name     = $this->row_class_name;
		$row_class      = new $class_name();
		if($row_class instanceof Vanilla_Model_Row)
		{
			return $row_class->getDAOInterface();
		}
		trigger_error("Can't find class:" . $class_name);
	}
	
	/**
	 * Check if field is unique 
	 * 
	 * @param string $field Field
	 * @param string $value Value
	 * 
	 * @return boolean
	 */
	public static function fieldIsUnique($field, $value)
	{
		$object = self::factory()->getAll(null, null, array($field => $value), null);
		if(count($object->rowset) == 0)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Filter results 
	 * @chainable
	 * @param string $filter_query
	 * @return Vanilla_Model_Rowset
	 */
	public function filter($filter_query)
	{
		$db_page = $this->getDAOInterface();
		$db_page->filter($filter_query);
		$this->assign($db_page);
		return $this;
	}
	
        /**
         * Filter rows to find columns that match the value
         * Used in autocomplete etc.
         * 
         * @param string $column Table Column
         * @param string $value  Value searched
         * 
         * @return \Vanilla_Model_Rowset 
         */
        public function filterAll($column)
        {
            $args    = func_get_args();
            $key     = $this->_getCacheKey(__FUNCTION__, $args);
            $db_data = Vanilla_Cache::get($key);

            if(false === $db_data)
            {
                    $db_data = $this->getDAOInterface();
                    $db_data->filterAll($column);
                    Vanilla_Cache::set($key, $db_data);
            }
            $this->assign($db_data);
            return $this;
        }
	
	/**
	 * get assoc array id => value
	 * @return array
	 */
	public function getAssocArray($field = 'name', $id_field = 'id')
	{
		$data = $this->toArray();
		if(empty($data))
		{
		    return array();
		}
		foreach($data as $row)
		{
			$_tmp[$row[$id_field]] = $row[$field];
		}
		return $_tmp;		
	}	
	
/**
	 * Get all images for entity
	 * 
	 * @param int $parent_id      Parent Id
	 * @param int $parent_type_id Parent Type Id
	 * @param int $limit          Limit
	 * 
	 * @return Media_Model_Images
	 */
    public function getAllForEntity($parent_id, $parent_type_id, $limit = null)
	{
	    $related = new Vanilla_Model_Relationships;
	    $related->getAllRelatedByIds($parent_id, $parent_type_id, $this->getEntityId(), $limit);
	    $ids     = $related->getAssocArray('child_id');
	    if(empty($ids))
	    {
	        return null;
	    }
	    
	    return self::getIdFromArray(null, null, $ids);
	}
	
	public function getEntityId()
	{
	    return $this->getRowObject()->entity_id;
	}
}
