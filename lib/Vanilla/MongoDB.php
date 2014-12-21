<?php
/**
 * Vanilla_MySQL Class
 *
 * @name     Vanilla_MySQL
 * @category Exception
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_MySQL Class
 *
 * @name     Vanilla_MongoDB
 * @category MySQL
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://www.php.net/manual/en/mongo.sqltomongo.php
 * @abstract
 */

abstract class Vanilla_MongoDB extends Vanilla_DataSource
{
    /**
     * Db Connection stored here
     * @var Vanilla_MySQL_Connection
     */
    protected $_db_connection;

    /**
     * All data collected with query
     * @var array
     */
    protected $_data = array();

    /**
     * List of colums available on the table
     * 
     * @var array
     */
    protected $_columns = array();

    /**
     * Number of found rows for this collection
     * @var int
     */
    protected $_rows_found = null;

    /**
     * Table we'll be using for our collection
     * @var unknown_type
     */
    protected $_table;

    /**
     * SQL query used to grab the data. This can be used for debugging if FirePHP is not working
     * @var string
     */
    protected $_sql;

    /**
     * Default relationship label name for the data to be recognised by
     * @var string
     */
    protected $_relationship_label_field = "name";

    /**
     * Constructor initiates the connection if none initiated
     * 
     * @return void
     */
    public function __construct()
    {
        // Connect to database
        if (null === $this->_db_connection) {
            $this->_db_connection = Vanilla_MongoDB_Connection::getInstance();
        }
    }

    public function filterAll($columns)
    {
        if(!is_array($columns))
        {
            $column = array($columns);
        }
        
        $collection = $this->getCollection();
        $cursor     = $collection->find(array(), $columns);
         
        if($cursor->count() > 0)
        {
            $this->_parseRowset($cursor);
            return $this;
        }
            
        return false;
    }
    
    /**
	 * Get all live and grouped.
	 * Grabs all live entities, that don't have a pending change
	 * or those changes
	 * 
	 * @param array  $params Parameters Array
	 * @param int    $limit  Limit
	 * @param int    $page   Page no
	 * @param string $order  Order string
	 * 
	 * @todo check if this works
	 * 
	 * @return Vanilla_MongoDB
	 */
    public function getAllLiveAndGrouped($params = null, $limit = null, $page = null,$order = null)
    {
        
        //$params     = $this->parseParams($params);
        $order      = $this->parseOrder($order);
        $collection = $this->getCollection();
        $skip       = (int) $limit * ($page - 1);
        
        // getting all records with original id
        $ids        = $collection->find(
            array(
            	'original_id' => array('$gt' => 0),
                'status'      => array('$ne' => (int) Vanilla_Model_Row::STATUS_DELETED)
            ), 
            array('original_id' => 1)
        )->toArray();
        
        $params['status'] = array('$ne' => (int) Vanilla_Model_Row::STATUS_DELETED);
        $params['id']     = array('$nin' => $ids);
        
        $cursor = $collection
            ->find($params)
            ->limit($limit)
            ->skip($skip)
            ->sort($order);
            
            
        $this->_parseRowset($cursor);
        return $this; 
    }

    /**
     * Returns SQL query used to get this record/s
     * 
     * @return string
     */

    public function getSql()
    {
        return $this->_sql;
    }

    /**
     * Parse Single Row, Throws Exception when more rows returned
     * 
     * @param array|null $row Result array
     * 
     * @return Vanilla_MongoDB
     */

    protected function _parseRow($row)
    {
        $this->_data[] = $row;
        return $this;
    }

    /**
     * Parsing a Collection of Rows Throws
     * 
     * @param MongoDB_Cursor $cursor MongoDB Cursor object
     * 
     * @return Vanilla_MongoDB
     */

    protected function _parseRowset(MongoCursor $cursor)
    {
        foreach($cursor as $row)
        {
            if($row['_id'] != "ai_id")
            {
                $this->_data[] = $row;
            }
            unset($row);
            gc_collect_cycles();
        }
        unset($cursor);
        return $this;
    }

    /**
     * Get number of rows found
     * 
     * @return int
     */
    public function getRowsFound()
    {
        return $this->_rows_found;
    }

    /**
     * Change pages order, $dir symbolizes direction up or down
     * 
     * @param int    $id           Object Id
     * @param int    $parent_id    Parent Object Id
     * @param string $parent_label Describe label for parent entity
     * @param int    $order        Order number
     * @param string $dir          up or down
     * 
     * @return void
     */

    public function changeOrder($id, $parent_id, $parent_label = "parent_id", $order = "1", $dir = "up")
    {
        
        $params     = array(
        	'status' => array ('$ne' => Vanilla_Model_Row::STATUS_DELETED),
            'parent_label' => $parent_id,
        );
        
        $total_count = $this->getCollection()->find($params)->count();

        if ($dir == "up") {
            $new_order = $order - 1;
            if ($new_order < 1) $new_order = 1;
        } else {
            $new_order = $order + 1;
            if ($new_order > $page_count) $new_order = $total_count;
        }
         
        // checking if need to move the old objects out
        if ($parent_id > 0) {
            $row_to_replace = $this->getByParams(array($parent_label => $parent_id, 'order' => $new_order))->getData();
            if (!empty($row_to_replace)) {
                $replaced_id = $row_to_replace[0]['id'];
                $this->update(array('order'=>$order), $replaced_id);
            }
        }
         
        // update the original object
        $this->getCollection()->update(array('order' => $new_order), $id);
    }

    /**
     * This is a hack to avoid 
     * Enter description here ...
     */
    public function getId()
    {
        $params = array("_id" => "ai_id");
        $cursor = $this->getCollection($backup)->findOne($params);
        
        if($cursor === null)
        {
            $this->createAutoincrementId($params);
        }
        
        $cursor = $this->_db_connection->command(
            array(
            	"findandmodify" => $this->_table,
                'query'  => $params,
                'new'    => true,
                'update' => array('$inc' => array('c' => 1))
            )
        );
        
        return $cursor['value']['c'];
    }

    public function createAutoincrementId($params)
    {
        $collection = $this->getCollection($backup);
        $cursor = $collection->find()->limit(1)->sort(array('id' => -1));
        if($cursor->count() > 0)
        {
            foreach($cursor as $row)
            {
                $data      = $params;
                $data['c'] = (int) $row['id'];
                return $collection->insert($data);
            }
        }
        else
        {
            $data      = $params;
            $data['c'] = 0;
            return $collection->insert($data);
        }
    }
    
    /**
     * Default Create Record script
     * Based on the $data array passed, creates new record
     * If asked for backup, creates the data in $table."_backup" table
     * 
     * @param array   $data   Data to insert into DB
     * @param booleam $backup Do we need backup?
     * 
     * @return int Id
     */


    public function create($data, $backup = false)
    {
        $collection = $this->getCollection($backup);
        $data['id'] = $this->getId();
        if($collection->insert($data))
        {
            return $data['id'];
        }
        return null;
    }

    /**
     * Deleting the row from database
     * 
     * @param array $data Data to be inserted array
     * 
     * @return bool
     */

    public function delete($data)
    {
        $collection = $this->getCollection();
        
        if (!is_array($data)) 
        {
            $params['id'] = (int) $data;
        }
    	else
    	{
    	    $params = $data;
    	}
    	
    	
        return $collection->remove($params);
    }


    /**
     * Creates backup table
     * 
     * @param array $data Data to be backed up
     * 
     * @return int Id
     */

    public function backup($data)
    {
        unset($data['needsBackup']
            , $data['db_class_name']
            , $data['required_fields']
            , $data['relations']
            , $data['children']
            , $data['errors']
            , $data['data_source_type']
        );
        return $this->create($data, true);
    }

    /**
     * Default update of data for given id,
     * replaces current values, of passed keys, with new ones
     * 
     * @param array   $data    Data to update the table with
     * @param int     $id      Id of updated element
     * @param boolean $partial Allows for updating only certain values (slower does an extra query to find column names)
     * 
     * @return mysqli_result
     */

    public function update($data, $id, $partial=false)
    {
        $collection = $this->getCollection();
        return $collection->update(array('id' => $id), array('$set' => $data));
    }

    /**
     * Get columns for this table
     * 
     * @return array
     */

    public function getTableColumns()
    {
        $collection = $this->getCollection();
        $params = array(
            '_id' => array(
                '$ne' => 'ai_id'
            )
        );
        $cursor = $collection->findOne($params);
        
        unset($cursor['_id']);
        
        $names = array_keys($cursor);   

        $this->_columns = $names;
        return $this->_columns;
    }
    
    /**
     * Default get total count of elements of the table
     * 
     * @param array $params Params for count
     * 
     * @return int Number of items that fit the query
     */
    public function getCount($params = null)
    {
        $params = $this->parseParams($params);
        $params['_id'] = array('$ne' => 'ai_id');
        
        $collection = $this->getCollection();
        $count      = $collection->find($params)->count();

        return $count; 
    }


    /**
     * Get collection object. If is backup is true,
     * the collection used will be the _backup one
     * 
     * @param boolean $is_backup Is backup
     * 
     * @return MongoCollection
     */
    public function getCollection($is_backup = false)
    {
        $table = $this->_table;
        if($is_backup === true)
        {
            $table .= "_backup";
        }
        $collection =  $this->_db_connection->$table;
        return $collection;
    }
    
    /**
     * Parsing params from the way we pass them to SQL to fit with
     * MongoDB structure
     * 
     * @param array $params
     */
    public function parseParams($params = null)
    {
        if($params === null)
        {
            return array();
        }
        foreach($params as $key => $value)
        {
            if(is_array($value))
            {
                if(!isset($value['operator']) || $value['operator'] == "=")
                {
                    if(is_array($value['value']))
                    {
                        $_tmp[$key] = array('$in' => $value['value']);
                    }
                    else
                    {
                        if(trim($value['value']) == "")
                        {
                            $_tmp[$key] = array('$exists' => false);
                        }
                        else
                        {
                            $_tmp[$key] = $value['value'];
                        }
                    }
                    
                }
                else 
                {
                    switch($value['operator'])
                    {
                        case "!=":
                        case "<>":
                            if(!is_array($value['value']) && trim($value['value']) == "")
                            {
                                $_tmp[$key] = array('$exists' => true);
                            }
                            else 
                            {
                                $_tmp[$key] = array('$ne' => $value['value']);
                            }
                            break;
                        case "=":
                            if(!is_array($value['value']) && trim($value['value']) == "")
                            {
                                $_tmp[$key] = array('$exists' => false);
                            }
                            else 
                            {
                                $_tmp[$key] = $value['value'];
                            }
                            break;
                        default:
                            echo "<pre>";
                            print_r($params);
                            trigger_error("don't know this param operator");
                            die;
                            break;
                    }
                    
                }
            }
            else 
            {
                if(trim($value) == "")
                {
                    $_tmp[$key] = array('$exists' => false);
                }
                else
                {
                    $_tmp[$key] = $value;
                }
            }
        }
        
        return $_tmp;
    }
    
    public function deleteWhere($params)
    {
        return $this->delete($params);
    }
    
    public function parseColumns($columns = "*")
    {
        if($columns === "*")
        {
            return array();
        }
        
    }
    
    public function parseSkip($limit = null, $page = null)
    {
        if($page === null || $limit === null)
        {
            return 0;
        }

        if($page > 1)
        {
            return (int) $limit * ($page - 1) + 1;
        }

        return (int) $limit * ($page - 1);
    }
    
    /**
     * Default Get Collection of All records matching the $params and in given order
     * for pagination, pass $limit and $page
     * $params can be a multi dimensional layer, and you can specify operators (!=, >, < etc)
     * 
     * @param int    $limit    Limit of elements per page
     * @param int    $page     Which page are we showing
     * @param array  $params   Params for WHERE statement
     * @param string $order    Order by
     * @param string $group_by Group By string
     * @param string $columns  Columns to fetch
     * @param string $joins    Joins
     * @param string $having   Having
     * 
     * @return Vanilla_MySQL
     */

    public function getAll($limit = null
        , $page = null
        , $params = null
        , $order = null
        , $group_by = null
        , $columns = "*"
        , $joins = null
        , $having = null
    )
    {
        
        $params     = $this->parseParams($params);
        $order      = $this->parseOrder($order);
        $collection = $this->getCollection();
        $columns    = $this->parseColumns($columns);
        $skip       = $this->parseSkip($limit, $page);
        $limit      = $this->parseLimit($limit, $page);
//echo $skip . ', ' . $limit . '<br />';
        $cursor     = $collection
            ->find($params, $columns)
            ->limit($limit)
            ->skip($skip)
            ->sort($order); 
            
        $this->_parseRowset($cursor);
        
        return $this; 
                    
    }

    public function parseLimit($limit = null, $page = null)
    {
        if($limit !== null && $page == 1)
        {
            return $limit + 1;
        }

        return $limit;
    }
    
    public function parseOrder($order = null)
    {
        if($order !== null)
        {
            $_tmp  = explode(" ", $order);
            $field = trim($_tmp[0]);
            if(count($_tmp) > 1 && preg_match("/ DESC/", $order))
            {
                $direction = -1;
            } 
            else 
            {
                $direction = 1;
            }
            return array($field => $direction);
        }
        return array();
    }
    
	protected function _parseLimitToSQL($limit = null, $page = null)
	{
		$sql = null;
		if (null !== $limit) {
            if (null !== $page) {
                $sql .= " LIMIT ".$limit * ($page - 1) . ",". $limit;
            } else {
                $sql .= " LIMIT ".$limit;
            }
        }
		return $sql;
	}
/*
    protected function _paramsArrayToWhereStatement($params = null)
    {
        if($params == null)
        {
            return null;
        }
        $x    = 0;
        foreach ($params as $key => $value)
        {
            if (!is_array($value))
            {
                $sql .= "`". $this->_table ."`.`".$key."` = " .$this->parseValue($value);
            }
            else
            {
                if (!is_array($value['value']))
                {
                    // can pass operators now i.e. 
                    // $params = array('type_id' => array('operator' => '>', 'value' => 3));
                    $sql .= "`". $this->_table ."`.`".$key."` " 
                         .$value['operator']. " " 
                         .$this->parseValue($value['value']);
                    }
                    else
                    {
                        $values = implode(',', $value['value']);
                        // where the value is an array
                        $sql .= "`". $this->_table ."`.`".$key."` IN (" .$values . ")";
                    }
                }
                $x++;
                if ($x != count($params)) {
                    $sql .= " AND ";
                }
        }
        return $sql;
    }
    */
    /**
     * Get elements from ID array
     * 
     * @param int   $limit Limit of elements
     * @param int   $page  Page of elements grabbed
     * @param array $ids   ID array
     * 
     * @return Vanilla_MySQL
     */
    public function getIdFromArray($limit = null, $page = null, $ids = array())
    {
        if(empty($ids))
        {
            return $this;
        }
        
        $ids = $this->arrayToInt($ids);
        
        $collection = $this->getCollection();
        $skip       = $this->parseSkip($limit, $page);
        $params     = array('id' => array ('$in' => $ids));
        
        $cursor     = $collection
            ->find($params)
            ->limit($limit)
            ->skip($skip);
            
        $this->_parseRowset($cursor);
        return $this; 
        
    }

    public function arrayToInt($array)
    {
        foreach($array as &$element)
        {
            $element = (int) $element;
        }
        return $array;
    }
    
    /**
     * Reorder remaining elements
     * 
     * @param array $data      Data array
     * @param int   $old_order Old order integer
     * @param int   $new_order New order integer
     * 
     * @chainable
     * 
     * @return Vanilla_MySQL
     */
    public function reorderOthers(array $data, $old_order, $new_order)
    {
        $sql = "UPDATE `". $this->_table ."` SET `order`= `order` + 1 WHERE `order` >= " .$new_order
        . " AND `id != ". $data['id'];
        $result = $this->query($sql);

        $sql = "UPDATE `". $this->_table ."` SET `order`= `order` + 1 WHERE `order` > " .$old_order
        . " AND `order` < " .$new_order
        . " AND `id` != ". $data['id'];
        $result = $this->query($sql);
        return $this;
    }

    /**
     * Default Get Record By Id
     * 
     * @param int $id Record ID
     * 
     * @return Vanilla_MySQL
     */

    public function getById($id)
    {
        $id = (int) $id;
        return $this->getByField('id', $id);
    }

    /**
     * Default Get Record By column name
     * 
     * @param string $field_name  Field Name
     * @param string $field_value Field Value
     * 
     * @return Vanilla_MongoDB
     */

    public function getByField($field_name, $field_value)
    {
        $params = array($field_name => $field_value);
        
        $collection = $this->getCollection();
        $cursor     = $collection->findOne($params);
        
        $this->_parseRow($cursor);
        return $this;
    }

    /**
     * Getting a record by params specified
     * 
     * @param array $params Params
     * 
     * @return Vanilla_MySQL
     */

    public function getByParams($params)
    {
        return $this->getAll(1, null, $params);
    }

    /**
     * Searching for relationship records
     * 
     * @param string $query Query String
     * @param int    $limit Limit of elements per page
     *    
     * @return Vanilla_MySQL
     */

    public function findForRelationship($query, $limit)
    {
        $sql = "SELECT `".$this->_relationship_label_field."` AS label "
        . ", id AS value"
        . " FROM `". $this->_table ."` WHERE `"
        .$this->_relationship_label_field."` LIKE \"" 
        . $this->_db_connection->real_escape_string($query)
        . "%\" LIMIT ".$limit;
        
        $result = $this->query($sql);
        $this->_parseRow($result);
        return $this;
    }
    
    public function getNextOrder()
    {
        $collection = $this->getCollection();
        $cursor     = $collection->find(array())->limit(1)->sort(array('order' => -1));
        
        $this->_parseRowset($cursor);

        return $this->_data[0]['order'] + 1;
        
    }

    /**
     * Create order for the page
     * 
     * @param int    $parent_id    Parent Id of the object
     * @param string $parent_field Parent field
     * 
     * @todo investigate what it actually does and write up, rename the function so that it's self explanatory
     * 
     * @return int
     */

    public function getNextOrderNumber($parent_id, $parent_field = 'parent_id')
    {
        
        $sql ="SELECT max(`order`)+1 as `newOrder` FROM `".$this->_table
        ."` WHERE `status` = ".Vanilla_Model_Row::STATUS_LIVE
        ." AND `".$parent_field."` = " . $parent_id;
        
        $result = $this->query($sql);
        $row_count = (int) $result->num_rows;
        if ($row_count > 0) {
            $this->_parseRow($result);
        }

        if ($this->_data[0]['newOrder'] == 0) {
            return 1;
        }
        else return $this->_data[0]['newOrder'];
    }

    /**
     * Get table name specified
     * 
     * @return string
     */

    public function getTableName()
    {
        return $this->_table;
    }

    /**
     * Get related field label specified
     * 
     * @return string
     */

    public function getRelatedEntityLabelField()
    {
        return $this->_relationship_label_field;
    }


}
