<?php

/**
 * 
 * @name Vanilla_Model_Row
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @abstract
 * 
 */

abstract class Vanilla_Model_Row extends Vanilla_Model_BasicObject
{
	/**
	 * TRACKING: Is tracking on. We're switching it off by default.
	 * @var boolean
	 */
	const TRACK_ON = false;
	
	/**
	 * List of fields that are required which will be checked for validation
	 * @var array
	 */
	public $required_fields = array();
	
	/**
	 * List of fields we want to skip for validation
	 * @var array
	 */
	public $skip_fields = array();
	
	/**
	 * Array of errors
	 * @var array
	 */
	public $errors;
	
	/**
	 * Default setting whether a row needs backup
	 * @var boolean
	 */
	public $needsBackup = false;
	
	/**
	 * Name of corresponding Data source class
	 * @var string
	 */
	public $db_class_name;
	
	/**
	 * Change this variable to change data source
	 * @var string
	 */
	public $data_source_type = "MongoDB"; // 
	
	/**
	 * Relations collection
	 * @var Array
	 */
	public $relations = array();
	
	/**
	 * Set Module name
	 * @var string
	 */
	
	public $module;
	
	public $track_on = self::TRACK_ON;
	
	public $users_permissions;
	
	public $id = 0;
	
	/**
	 * You can overwrite the default edit template in here
	 */
	public $form_template   = null;
	
	private $_columns;
	
	public $assign_entities = array();
	
	public $images;
	
	/**
	 * Check if blog exists
	 * 
	 * @return boolean
	 */
	public function exists()
	{
	    if($this->id > 0)
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Factory new object
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Model_Row
	 */
	public static function factory()
	{
		$class = get_called_class();
		$object = new $class();
		return $object;
	}
	
	public function getAllowedEntities($entities_string = null)
	{
	    $entities = new Vanilla_Model_EntityTypes;
	    
	    if($entities_string === null)
	    {
	        $entities_array = $this->assign_entities;
	    }
	    else
	    {
	        $entities_array = explode(",", $entities_string);
	    }
	        
	    if(empty($entities_array))
	    {
            $entities->getAll();
	    }
	    else 
	    {
	        $entities->getIdFromArray(null, null, $entities_array);
	    }
	    return $entities->rowset;
	}
	
	public function getDataSourceColumns()
	{
		return get_class_vars($this->_columns);
	}
	
	/**
	 * Fetches all the columns from the database
	 * and iterates through the data array unsetting any fields
	 * that don't fit 
	 * 
	 * @param array $data Data array
	 * 
	 * @return array
	 */
	public function returnOnlyTableColumns($data)
	{
	    $original_data = $data;
	    if($this->_columns === null)
	    {
	        $this->_columns = $this->getDAOInterface()->getTableColumns();
	    }
	    foreach($data as $key => $value)
	    {
	        if(!in_array($key, $this->_columns))
	        {
	            unset($data[$key]);
	        }
	    }
	    
	    if($original_data !== $data)
	    {
	        Vanilla_Debug::Error("Array of data you were trying to save is different from the column structure.");
	    }
	    
	    return $data;
	}
	
	public function getNextOrder()
	{
	    return $this->getDAOInterface()->getNextOrder();
	}
	
	public function addDefaultPermissions()
	{
	    $permissions_constant_name = "PERMISSIONS_" . strtoupper($this->module)."_DEFAULT";
	    if(Vanilla_Module::isInstalled("Users") && defined($permissions_constant_name))
	    {
	        $data['entity_id']       = $this->id;
	        $data['entity_types_id'] = $this->entity_id;
	        $constant_value          = constant($permissions_constant_name);
	        if($constant_value == "all")
	        {
	            $groups = Users_Model_User_Groups::factory()->getAll(null, null, array('status' => Vanilla_Model_Row::STATUS_LIVE));
	        }   
	        else
	        {
	            $group_ids = explode(",", $constant_value);
	            $groups = Users_Model_User_Groups::factory()->getIdFromArray(null, null, $group_ids);   
	        } 
	        if(!empty($groups->rowset))
	        {
    	        foreach($groups->rowset as $group)
    	        {
    	            $data['user_group_id'] = $group->id;
    	            Users_Model_Permission::factory()->create($data);
    	        }
	        }
	        
	    }
	}
	
	/**
	 * Setting new order position for the record
	 * 
	 * @param int $new_order New Order
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Model_Row
	 */
	public function setNewOrder($new_order)
	{
		$old_order = $this->order;
		if($new_order == $old_order)
		{
			return $this;
		}
		// set new order
		if($new_order > $old_order)
		{
			$this->_orderGoDown($new_order, $old_order);
		}
		else 
		{
			$this->_orderGoUp($new_order, $old_order);
		}
		$this->update(array('order' => $new_order));
		return $this;
	}
	
	/**
	 * Get Users Permissions for this Entity
	 * Searches the Users Permissions database for correct user_group
	 * and returns an array of user_group ids
	 * 
	 * @return array;
	 */
	public function getUserPermissionsGroups()
	{
	    if(Vanilla_Module::isInstalled("Users"))
	    {
	        if (empty($this->entity_id))
	        {
	            var_dump($this);
	        }
	        $params = array(
	        	'entity_id' => $this->id,
	            'entity_types_id' => $this->entity_id
	        ); 
	        $permissions             = Users_Model_Permissions::factory()->getAll(null, null, $params);
	        $tmp_users_permissions = $permissions->getAssocArray('user_group_id');
	    }
	    

            if(isset($tmp_users_permissions)){
                if($this->users_permissions === null)
                {
                    $this->users_permissions = $tmp_users_permissions;
                }
                //add any found permissions onto the already defined permissions
                else
                {
                    $this->users_permissions = array_merge($this->users_permissions, $tmp_users_permissions);
                }
            }
			
	    return $this->users_permissions;
	}
	
	/**
	 * Tranlsate this row to the language specified
	 * If no translation found, return row as it was
	 * 
	 * @param Languages_Model_Language $language Language
	 * 
	 * @return Vanilla_Model_Row
	 */
	public function translate($language = null)
	{
	    if(Vanilla_Module::isInstalled("Languages"))
	    {
	        if($language === null)
	        {
	            $locale  = Vanilla_Locale::getFromSession();
	            $language = $locale->getLanguageObject();
	            
	        }
    	    $translator    = $this->getTranslateObject();
    	    $params        = array('language_id' => $language->id, $translator->primary_id => $this->id);
    	    
    	    $translator->getByParams($params);
    	    if($translator->id > 0)
    	    {
        	    foreach($translator->translated_fields as $field)
        	    {
        	        $this->$field = $translator->$field;
        	    }
    	    }
	    }
	    
	    return $this;
	}
	
	/**
	 * Getting Translate Object
	 * The Translate Object should always correspond with the Class name
	 * e.g. for Pages_Model_Content_Type we're looking for Languages_Translate_Content_Type
	 * so we're exploding the class name searching for underscores, and then setting 
	 * anything after Model as a reminder, that will be appended after Languages_Translate
	 * 
	 * @return Object
	 */
	public function getTranslateObject()
	{
	    $class_name   = get_called_class();
	    $_tmp         = explode("_", $class_name);
	    $passed_model = false;
	    foreach($_tmp as $part)
	    {
	        if($passed_model === true)
	        {
	            $reminder[] = $part;
	        }
	        if($part == "Model")
	        {
	            $passed_model = true;
	        }
	    }
	    $class_reminder   = implode("_", $reminder);
	    $class_name       = "Languages_Model_Translate_" . $class_reminder;
	    $translate_object = new $class_name(); 
	    return $translate_object;
	}
	
	/**
	 * Order other entities for going down
	 * 
	 * @param int $new_order New Order Int
	 * @param int $old_order Old Order Int
	 * 
	 * @return void
	 */
	protected function _orderGoUp($new_order, $old_order)
	{
		$order_array['value'] = range($new_order, $old_order - 1);
		$collection           = $this->_getCollectionForOrdering($order_array);
		foreach($collection->rowset as $row)
		{
			$order = $row->order + 1;
			$row->update(array('order' => $order));
		}
	}

	/**
	 * Order other entities for going down
	 * @param int $new_order
	 * @param int $old_order
	 * @return void
	 */
	protected function _orderGoDown($new_order, $old_order)
	{
		$order_array['value'] = range($old_order + 1, $new_order);
		$collection           = $this->_getCollectionForOrdering($order_array);
		foreach($collection->rowset as $row)
		{
			$order = $row->order - 1;
			$row->update(array('order' => $order));
		}
	}
	
	/**
	 * Getting the collection of the elements between the old position and new one
	 * to get them ready for the update
	 * @param array $order_array
	 * @return Vanilla_Model_Rowset
	 */
	protected function _getCollectionForOrdering($order_array)
	{
		$params = array('order' => $order_array);
		$collection    = $this->getRowset()->getAll(null, null, $params);
		return $collection;
	}
	
	/**
	 * Returns a rowset
	 * @return Vanilla_Model_Rowset
	 */
	public function getRowset()
	{
		$class_name    = get_called_class()."s";
		$rowset_object = new $class_name(); 
		return $rowset_object;
	}
	
	/**
	 * Reorder other entries
	 * @param int $old_order
	 * @param int $new_order
	 * @chainable
	 * @return Vanilla_Model_Row
	 */
	private function _reorderOthers($old_order, $new_order)
	{
		$db_data     = $this->getDAOInterface();
		$db_data->reorderOthers($this->toArray(), $old_order, $new_order);
		return $this;
	}
	
	/**
	 * Assign the values to the class that we collected from Data Source
	 * 
	 * @param Vanilla_MySQL $db_record Data Source Record
	 * 
	 * @return Vanilla_Model_Row
	 */
	public function assign($db_record)
	{
		if($db_record instanceof Vanilla_DataSource)
		{
			$db_data = $db_record->getData();
		}
		else
		{
			$db_data = $db_record;
		}
		
		if(!empty($db_data))
		{
			if(isset($db_data[0]))
			{
				$db_data = $db_data[0];
			}
			
			foreach($db_data as $key => $value)
			{
				$key        = strtolower($key);
				$this->$key = $value;
			}
			unset($db_record);
			return $this;
		}
		else
		{
			//@todo throw error;
			return null;
		}
	}
	
	/**
	 * Approve entity
	 * 
	 * @return void
	 */
	public function approve()
	{
	    $acl  = new Vanilla_ACL();
	    $user = $acl->getLoggedUser();
	    
	    if($this->original_id > 0)
        {
            $original   = $this->factory();
            $original->getById($this->original_id);
            
            $original_data                   = $original->toDatabaseArray();
            $update_data                     = $this->toDatabaseArray();
            $update_data['approved_user_id'] = $user->id;
            $update_data['status']           = Vanilla_Model_Row::STATUS_LIVE;
            $update_data['original_id'] = 0;
            $original->update($update_data);
            $original_data['status']      = Vanilla_Model_Row::STATUS_DELETED;
            $original_data['original_id'] = $original->id;
            $this->update($original_data);
        }
        else 
        {
            $data                     = array();
            $data['status']           = Vanilla_Model_Row::STATUS_LIVE;
            $data['approved_user_id'] = $user->id;
            $this->update($data);
        }
	}
	
	
	
	/**
	 * Turn object into array
	 * 
	 * @return Array
	 */
	
	public function toArray()
	{
		return get_object_vars($this);
	}
	
	public function toDatabaseArray()
	{
	    $columns = $this->getDAOInterface()->getTableColumns();
	    $data    = get_object_vars($this);
	    foreach($data as $column => $value)
	    {
	        if(in_array($column, $columns))
	        {
	            $_tmp[$column] = $value;
	        }
	    }
	    return $_tmp;
	}
	
	/**
	 * 
	 * Add model data to data source
	 * @param Array $data
	 * @chainable
	 * @return Vanilla_Model_Row
	 */
	
	public function create($data)
	{
            foreach($data as $key => $value)
            {
                    $this->$key = $value;	
            }

            $db_data     = $this->getDAOInterface();
            $this->id    = $db_data->create($data);
            if($this->id > 0)
            {
                Vanilla_Cache::flush();
            }
            return $this;
	}
	
	/**
	 * 
	 * Get Next Order Number
	 * @param int $parent_id
	 * @param string $parent_field
	 * @return Vanilla_Model_Row
	 */
	
	public function getNextOrderNumber($parent_id, $parent_field = 'parent_id')
	{
		$db_data     = $this->getDAOInterface();
		$this->order = $db_data->getNextOrderNumber($parent_id, $parent_field);
		return $this->order;
	}
	
	/**
	 * Set Class to use a different Data Source
	 * @param string $data_source_type
	 */
	
	public function setDataSource($data_source_type)
	{
		$this->data_source_type = $data_source_type;
	}
	
	/**
	 * Delete Row from Data Source
	 * @param array $data;
	 */
	
	public function delete($data = null)
	{
		$db_data = $this->getDAOInterface();
		$data    = ($data == null)? $this->id : $data;
		$db_data->delete($data);
		Vanilla_Cache::flush();
		return $this;
	}
	
	/**
	 * Update values for model
	 * 
	 * @param Array $data
	 * @param boolean $partial Allows for updating only certain values (slower does an extra query to find column names)
	 * 
	 * @return Model_Vanilla_Row
	 */
	
	public function update($data, $partial=false)
	{
	    if(empty($data))
	    {
	        return null;
	    }
	    if(isset($data['id']))
	    {
	        unset($data['id']);
	    }
		$db_data     = $this->getDAOInterface();
		// checking if this model requires backup
		if($this->needsBackup)
		{
			$db_data->backup($this->toArray());
		}
		$result = $db_data->update($data, $this->id, $partial);
               
		if($result)
		{
                   
			$this->assign($data);
			Vanilla_Cache::flush();
                      
			return $this;
		}
		return false;
	}
	
	/**
	 * 
	 * Validate data
	 * Returns array of errors, or true if it validates
	 * @param array $data
	 * @param array $skip_fields
	 * @return mixed
	 */
	
	public function validate($data, $skip_fields = array())
	{
		
	    if(!empty($this->required_fields))
		{
			foreach($this->required_fields as $field)
			{
				if(is_array($skip_fields) && !in_array($field, $skip_fields) && empty($data[$field]))
				{
					$this->errors[] = parse_name_to_word($field) . " is a required field.";
				}
			}
		}
		
		if(count($this->errors) > 0) 
		{
			return $this->errors;
		}
		return true;
	}
	
	
	/**
	 * Admin Validate
	 * Overwrite required fields with admin_required_fields if they exist
	 * @param array $data
	 * @param array $skip_fields
	 * @return mixed
	 */
	public function validateAdmin($data, $skip_fields = array())
	{
		if(!empty($this->admin_required_fields))
		{
			$this->required_fields = $this->admin_required_fields;
		}
		return $this->validate($data, $skip_fields);
	}
	
	
	/**
	 * Getting all related elements from Relationships table backwards
	 * Developer can specify the type as string parameter
	 * @param string $type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * 
	 */
	
	public function getRelatedReverse($type = null, $recursive = false, $order = null)
	{
		$relationships = new Vanilla_Model_Relationships();
		$table = $this->getTableName();
			// hack to get 'people' to change to 'persons'
				if (in_array($table, Vanilla_Admin::$_remap_modules)) {
					foreach (Vanilla_Admin::$_remap_modules as $module => $remap) {
						if ($remap == $table) {
							$table = $module;
						}
					}
				}
			// end hack
		$this->relations = $relationships->getAllRelatedReverse($this->id, $table, $type, $recursive, null, null, $order);
		return $this;
	}
		/**
	 * Getting all related elements from Relationships table
	 * Developer can specify the type as string parameter
	 * @param string $type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * 
	 */
	
	public function getRelated($type = null, $recursive = false, $order = 'order', $return = "object")
	{
	    if(is_numeric($this->id))
		{
    		$relationships = new Vanilla_Model_Relationships();
    		$table = $this->getTableName();
    		// another hack to change portfolios from pages to get 
    		// the related items based on the correct entity_type_id
    		if($this->module == "Portfolio")
    		{
    		    $table = "projects";
    		}
    		
    		// hack to get 'people' to change to 'persons'
    			if (in_array($table, Vanilla_Admin::$_remap_modules)) {
    				foreach (Vanilla_Admin::$_remap_modules as $module => $remap) {
    					if($remap == $table) {
    						$table = $module;
    					}
    				}
    			}
    		// end hack
    		$this->relations = $relationships->getAllRelated($this->id, $table, $type, $recursive, null, null, $order, $return);
		}
		return $this;
	}
	
	/**
	 * Getting all related elements from Relationships table, checking both ways!
	 * Developer can specify the type as string parameter
	 * @param string $type
	 * @param boolean $recursive (not fully recursive, only goes one level down)
	 * 
	 */
	
	public function getRelatedBothWays($type = null, $recursive = false, $order = null)
	{
		
		$relationships = new Vanilla_Model_Relationships();
		$table = $this->getTableName();
			// hack to get 'people' to change to 'persons'
				if (in_array($table, Vanilla_Admin::$_remap_modules)) {
					foreach (Vanilla_Admin::$_remap_modules as $module => $remap) {
						if ($remap == $table) {
							$table = $module;
						}
					}
				}
			// end hack
		$this->relations = $relationships->getRelatedBothWays($this->id, $table, $type, $recursive, null, null, $order);
		return $this;
	}	
	
	
	/**
	 * Delete all related elements from Relationships table
	 * Developer can specify the type as string parameter
	 * 
	 * @return void
	 */
	public function deleteRelatedEntities()
	{
		Vanilla_Model_Relationship::factory()->delete(array('child_id' => $this->id, 'child_entity_type_id' => $this->entity_id));
		Vanilla_Model_Relationship::factory()->delete(array('parent_id' => $this->id, 'parent_entity_type_id' => $this->entity_id));
	}

	/**
	 * Getting all related elements from Relationships table
	 * Developer can specify the type as string parameter
	 * 
	 * FIXME: $this->id is actually used as user_group_id (meaning this function might only make sense on the UserGroup class?)
	 * 
	 */
	
	public function getPermissions()
	{
		$permissions = new Users_Model_Permissions();
		$this->permissions = $permissions->getAllPermissions($this->id);
	}	
	
	/**
	 * Get row of data by primary id
	 * @param int $id
	 * @return Vanilla_Model_Row
	 */
	public function getById($id)
	{
		$arguments = func_get_args();
		$key       = $this->_getCacheKey(__FUNCTION__, $arguments);
		$db_data   = Vanilla_Cache::get($key);
		
		if(false === $db_data)
		{
		    $id      = (int) $id;
			$db_data = $this->getDAOInterface();
			$db_data->getById($id);
			Vanilla_Cache::set($key, $db_data);
		}
		$this->assign($db_data);
		return $this;
	}
	
	/**
	 * Get row of data by field_name
	 * @param string $field_name
	 * @param string $field_value
	 * @return Vanilla_Model_Row
	 */
	
	public function getByField($field_name, $field_value)
	{
		$key     = $this->_getCacheKey(__FUNCTION__, func_get_args());
		$db_data = Vanilla_Cache::get($key);
		if(false == $db_data)
		{
			$db_data = $this->getDAOInterface();
			$db_data->getByField($field_name, $field_value);
			Vanilla_Cache::set($key, $db_data);
		}
		$this->assign($db_data);
		return $this;
	}
	
	/**
	 * 
	 * Getting a result that fits the params
	 * @param array $params
	 * @return Vanilla_Model_Row
	 * 
	 */
	
	public function getByParams($params)
	{
		$key     = $this->_getCacheKey(__FUNCTION__, func_get_args());
		$db_data = Vanilla_Cache::get($key);
		
		if(false === $db_data)
		{
			$db_data = $this->getDAOInterface();
			$db_data->getByParams($params);
			Vanilla_Cache::set($key, $db_data);
		}
		$this->assign($db_data);
		return $this;
	}
	
	/**
	 * 
	 * Returns Table Name of associated MySQL object
	 * @return string //table name
	 */
	
	public function getTableName()
	{
		$db_data = $this->getDAOInterface();
		return $db_data->getTableName();
	}
	
	/**
	 * 
	 * Returns Field Label name for the entity
	 * @return string // field name
	 */
	
	public function getRelatedEntityLabelField()
	{
		$db_data = $this->getDAOInterface();
		return $db_data->getRelatedEntityLabelField();
	}
	
	/**
	 * Generating Data Source Name
	 * @return string
	 */
	
	private function _getDataSourceClassName()
	{
	    if(defined("DB_ENGINE"))
	    {
	        $data_source = DB_ENGINE;
	    }
	    else
	    {
	        $data_source = "MySQL";
	    }
	    
		$class_name = $data_source ."_". ucwords($this->db_class_name);
		
		if(null !== $this->module)
		{
			$class_name = $this->module."_".$class_name;
		}
		if(class_exists($class_name))
		{
			// returns application specific Mysql
			return $class_name;
		}
		// returns Vanilla specific Mysql
		return "Vanilla_".$class_name;
	}
	
	/**
	 * Get Data Source Object
	 * For creating a corresponding MySQL, NoSQL etc object
	 * grabs specified instance from Row class, can be overwritten
	 * 
	 * @deprecated
	 * @chainable
	 * 
	 * @return Object 
	 */
	
	public function getDataSourceClassObject()
	{
		return $this->getDAOInterface();
	}
	
	/**
	 * Get DAO Interface
	 * Data access object (DAO) is an object that provides an abstract 
	 * interface to some type of database or persistence mechanism, 
	 * providing some specific operations without exposing details of 
	 * the database. It provides a mapping from application calls to the 
	 * persistence layer. This isolation separates the concerns of what 
	 * data accesses the application needs, in terms of domain-specific 
	 * objects and data types (the public interface of the DAO), and how 
	 * these needs can be satisfied with a specific DBMS, database 
	 * schema, etc. (the implementation of the DAO).
	 * 
	 * Initiates correct MySQL or NoSQL interface as specified in Row
	 * Object, or DB_ENGINE config.
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_DataSource
	 */
	
	public function getDAOInterface()
	{
	    $class_name = $this->_getDataSourceClassName();
		$object     = new $class_name();
		return $object;
	}
	/**
	 * Track the user entry
	 * @param int $user_id
	 * @param string $action
	 * @return boolean
	 */
	
	public function track($user_id, $action, $info = '')
	{
		//$class = get_called_class();
		if($this->track_on === true)
		{
			$data['entity_id']       = (int) $this->id;
			$data['entity_types_id'] = (int) $this->entity_id;
			$data['user_id']         = (int) $user_id;
			$data['action']          = $action;
			$data['info']            = $info;
			return Vanilla_Model_Track::factory()->create($data);
		}
	}
	
		/**
	 * 
	 * Relates an entity to a parent_entity
	 * 
	 * @param int $entity_id
	 * @param int $entity_type_id
	 */
	public function addRelationship($entity_id, $entity_type_id)
	{
	    if(empty($entity_id) || empty($entity_type_id))
	    {
	        trigger_error("Entity id nor entity_type_id can be empty");
	    }
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
	
	/**
	 * Getting basic db data for duplication
	 * @return array
	 */
	public function _getBasicDataForDuplication()
	{
		$table_columns = $this->getDAOInterface()->getTableColumns();
		foreach($table_columns as $column)
		{
			$data[$column] = $this->$column;
		}
		$data['date_created']  = date("Y-m-d H:i:s");
		$data['date_modified'] = "0000-00-00 00:00:00";
		unset($data['id']);
		return $data;
	}
	
 	/**
     * Save a new version of Content
     * 
     * @param array             $data     Data array of changes
     * @param Vanilla_Model_Row $original Original Object
     * 
     * @return void
     */
    public function saveVersionOfObject($data)
    {
        // check if we already have a pending version of this content
        $version_object = $this->factory()->getByField('original_id', $this->id);
        
        if($version_object->id > 0)
        {
            $version_object->update($data);
        }
        else 
        {
            // need to create new content that we can edit for approval
            $new_object    = $this->factory();
            $original_data = $this->toDatabaseArray();
            $original_data = array_merge($original_data, $data);
            unset($original_data['id']);
            
            $original_data['approved_user_id'] = 0;
            $original_data['original_id']      = $this->id;
            $original_data['status']           = Vanilla_Model_Row::STATUS_PENDING;
            $new_object->create($original_data);
        }
    }
	
	/**
	 * Checks the status of the current user
	 * 
	 * @return the status as defined in row
	 */
	public function getStatusForCreate($user)
	{
	    if($user->isAdmin())
	    {
	        return Vanilla_Model_Row::STATUS_LIVE;
	    }
	    return Vanilla_Model_Row::STATUS_PENDING;
	}
	
	/**
	 * Get image for any entity
	 * 
	 * @return Media_Model_Image
	 */
    public function getImage()
	{
	    if(Vanilla_Module::isInstalled('Media'))
	    {
	        if($this->images === null)
	        {
    	        $images = Media_Model_Images::factory()->getAllForEntity($this->id, $this->entity_id);
    	        if(empty($images->rowset))
    	        {
    	            $images->rowset[0]->path = "http://dummyimage.com/ccc/fff.png";
    	        }
    	        $this->images = $images;
	        }
	        return $this->images->rowset[0];
	    }
	    return null;
	}
	
	/**
	 * Get image for any entity
	 * 
	 * @return Media_Model_Image
	 */
    public function getImages()
	{
	    if(Vanilla_Module::isInstalled('Media'))
	    {
	        if(!isset($this->images) || $this->images === null)
	        {
	            $images = Model_Images::factory()->getAllForEntity($this->id, $this->entity_id);
	            $this->images = $images;
	        }
            if($this->images instanceof Media_Model_Images)
            {
	            return $this->images->rowset;
            }
	    }
	    return null;
	}
	
	public function getWidgets()
	{
	    if(Vanilla_Module::isInstalled('Widgets'))
	    {
	        if(!isset($this->widgets) || $this->widgets === null)
	        {
	            $widgets = Model_Widgets::factory()->getAllForEntity($this->id, $this->entity_id);
	            if(!empty($widgets->rowset))
	            {
    	            foreach($widgets->rowset as $widget)
    	            {
    	                $_tmp[] = Widgets_Widget_Factory::init($widget->class);
    	            }
    	            $this->widgets = $_tmp;
	            }
	        }
	        return $this->widgets;
	    }
	    return null;
	}
	
}
