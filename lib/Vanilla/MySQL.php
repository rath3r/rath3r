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
 * @name     Vanilla_MySQL
 * @category MySQL
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 * @abstract
 */

abstract class Vanilla_MySQL extends Vanilla_DataSource
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

    protected $_query_count;
    
    /**
     * Constructor initiates the connection if none initiated
     * 
     * @return void
     */
    public function __construct()
    {
        // Connect to database
        if (null === $this->_db_connection) {
            $this->_db_connection = Vanilla_MySQL_Connection::getInstance();
        }
    }

    /**
     * Factory new object
     * 
     * @chainable
     * 
     * @return Vanilla_Model_MySQL
     */
    public static function factory()
    {
        $class = get_called_class();
        $object = new $class();
        return $object;
    }

    /**
     * Disconnect from the database
     *
     * @return void
     */

    public function __destruct()
    {
        if ($this->_db_connection instanceof mysqli) {
            //$this->_db_connection->close();
            //$this->_db_connection = null;
        }
    }
    
    public function filterAll($column)
    {
        if(!is_array($column))
        {
            $column = array($column);
        }
        
        $sql     =    "SELECT ";
        $sql    .=    implode($column);
        $sql    .=    " FROM ".$this->_table;  
        
        $result = $this->query($sql);
        
        if($result->num_rows >0)
        {
            $this->_parseRowset($result);
            return $this;
        }
        
        return false;
    }
    /**
     * Queries the database, and if debug is on, displays SQL in FirePHP
     * Make sure this is always used to query the database, to keep debugging consistency
     * 
     * @param string $sql Sql           Query
     * @param bool   $return_connection Return connection
     * 
     * @final
     * 
     * @return mysqli_result
     */

    final public function query($sql)
    {
        $this->_data = null;
        $this->_sql  = $sql;
        $result      = $this->_db_connection->query($sql);
		
        // there was a problem with fetching a result, or set up asks
        // to display all mysql queries
        $result_errors = $this->_checkIfResultFetched($result);
        if ($result_errors || (defined('DEBUG_DB') && DEBUG_DB == true)) {
            $type = $result_errors ? "LOG" : "ERROR";
            Vanilla_Debug::SQL($sql, $type);
        }

        return $result;

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
    public function getAllLiveAndGrouped($params = null, $limit = null, $page = null,$order = null)
    {
        $sql  = "SELECT SQL_CALC_FOUND_ROWS ";
        $sql .= " * FROM `". $this->_table ."`";
        $sql .= " WHERE id NOT IN (SELECT original_id FROM `" . $this->_table . "` WHERE original_id > 0 AND status != ".Vanilla_Model_Row::STATUS_DELETED.")";
        $sql .= " AND status != ".Vanilla_Model_Row::STATUS_DELETED;
        $sql_params = $this->_paramsArrayToWhereStatement($params);

        if($sql_params !== null)
        {
            $sql .= " AND ".$sql_params;
        }
		
        
       
        if (null !== $order) {
           
            if (preg_match("/DESC/", $order, $matches)) {
                $order = explode("DESC", $order);
                $sql .= " ORDER BY `". $this->_table ."`.`".trim($order[0])."` DESC";
            } else {
                $sql .= " ORDER BY `". $this->_table ."`.`".$order."`";
            }
            
        }
        
	$sql .= $this->_parseLimitToSQL($limit, $page);
        
        
        // run query
        $result = $this->query($sql);
        $this->setQueryCount();
        $this->_parseRowset($result);
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
     * Checking for any MySQL Errors, and returning them if debug is enabled
     * 
     * @param mysqli_result $result Result Object
     * 
     * @return boolean
     */

    protected function _checkIfResultFetched($result)
    {
        if (!$result) {
            Vanilla_Debug::SQL($this->_db_connection->error, 'ERROR');
            return false;
        }
        return true;
    }

    /**
     * Return the Data array
     * 
     * @return array
     *
     */
     
    public function getData()
    {
        return $this->_data;
    }
     
    /**
     * Parse Single Row, Throws Exception when more rows returned
     * 
     * @param Mysqli_Result &$result Result Object
     * 
     * @throws Exception
     * 
     * @return Vanilla_MySQL
     */

    protected function _parseRow(Mysqli_Result &$result)
    {
        while ($row = $result->fetch_assoc()) {
            $this->_data[] = $row;
        }

        $result->free();
        if (count($row) > 0) {
            throw new Exception("Found more instances of the query in ". $this->_table);
        }
        return $this;
    }

    /**
     * Parsing a Collection of Rows Throws
     * 
     * @param Mysqli_Result &$result Result Object
     * 
     * @throws Exception
     * 
     * @return Vanilla_MySQL
     */

    protected function _parseRowset(Mysqli_Result &$result)
    {
        while ($row = $result->fetch_assoc()) {
            $this->_data[] = $row;
        }
        $result->free();
        return $this;
    }
    
    public function setQueryCount()
    {
        $sql    = "SELECT FOUND_ROWS();";
        $result = $this->query($sql);
        while ($row = $result->fetch_row()) {
            $this->_query_count = $row[0];
        }
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
        $sql    = "SELECT COUNT(*) AS `total_count` FROM `".$this->_table
                ."` WHERE `status` != ". Vanilla_Model_Row::STATUS_DELETED
                . "  AND `".$parent_label. "` = " . $parent_id;
                
        $result = $this->query($sql);
         
        $this->_parseRow($result);
        $total_count = $this->_data[0]['total_count'];

        if ($dir == "up") {
            $new_order = intval($order) - 1;
            //if ($new_order < 1) $new_order = 1;
        } else {
            $new_order = intval($order) + 1;
            //if ($new_order > $page_count) $new_order = $total_count;
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
        $this->update(array('order' => $new_order), $id);
        
        // clean up all children's orders just in case something strange has happened
        if ($parent_id > 0) {
            $params = array($parent_label => $parent_id);
            $rows = $this->getAll(null, null, $params, 'order')->getData();
            if (!empty($rows)) {
                $order_counter = 1;
                foreach($rows as $row){
                    $this->update(array('order'=>$order_counter), $row['id']);
                    $order_counter++;
                }
            }
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
        if ($backup === true) {
            $table_name = $this->_table."_backup";
        } else {
            $table_name = $this->_table;
        }
        
        $data = $this->purifyData($data);
        
        $db_columns = array_keys($data);
        $db_values  = array_values($data);
        foreach ($db_values as $key => $value) {
            $db_values[$key] = "'".$this->_db_connection->real_escape_string($value) ."'";
        }
        foreach ($db_columns as $key => $value) {
            $db_columns[$key] = "`".$this->_db_connection->real_escape_string($value) ."`";
        }

        $db_values_str = implode(",", $db_values);
        $db_columns_str = implode(",", $db_columns);

        $sql = "INSERT INTO `". $table_name ."` (". $db_columns_str .") VALUES (" . $db_values_str. ");";
        $result = $this->query($sql);

        
        return $this->_db_connection->insert_id;

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
       
        if (is_array($data)) {
            // delete using params
            $result = $this->deleteWhere($data);
        } else { 
            // delete by id
            $id  = (int) $data;
            $sql = "DELETE FROM `". $this->_table ."` WHERE `id` =".$this->_db_connection->real_escape_string($id).";";
            $result = $this->query($sql);
        }
        return $result;
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
        unset($data['needsBackup']);

        $data = $this->purifyData($data);
        
        if ($partial == true) {
            $this->getTableColumns();

            $sql = "UPDATE `". $this->_table ."` SET ";
            foreach ($this->_columns as $field_name) {
                if (isset($data[$field_name])) {
                    $set_query[] = " `".$field_name."` = " 
                        . $this->parseValue($this->_db_connection->real_escape_string($data[$field_name]));
                }
            }
            $sql .= implode(",", $set_query);
            $sql .= " WHERE `id`=".$id;
        } else {
            $sql = "UPDATE `". $this->_table ."` SET ";
            foreach ($data as $key => $value) {
                $value = $this->_db_connection->real_escape_string($value);
                $set_query[] = " `".$key."` = " . $this->parseValue($value);
            }
            $sql .= implode(",", $set_query);
            $sql .= " WHERE `id`=".$id;
        }
        $result = $this->query($sql);
        
        return $result;
    }
    
    /**
     * Using HTMLPurify to purify any saves
     * 
     * @param array $data Data to be saved
     * 
     * @return array
     */
    public function purifyData(array $data)
    {
        $purifier = new Vanilla_Purifier();
        foreach($data as $key => $value)
        {
            $data[$key] = $purifier->purify($value);
        }
        return $data;
    }

    /**
     * Get columns for this table
     * 
     * @return array
     */

    public function getTableColumns()
    {
        $sql = "SHOW COLUMNS FROM ". $this->_table;
        $column_result = $this->query($sql);
        $this->_parseRowset($column_result);

        foreach ($this->_data as $value) {
            $this->_columns[] = $value['Field'];
        }
        return $this->_columns;
    }
    
    /**
     * Returning query count 
     * 
     * @return int
     */
    public function getQueryCount()
    {
        return $this->_query_count;
    }

    /**
     * Default get total count of elements of the table
     * 
     * @param array $params Params for count
     * 
     * @return int Count
     */
    public function getCount($params = null)
    {
        $sql = "SELECT COUNT(*) AS count FROM `". $this->_table ."`";

        if (null !== $params) {
            $sql .= " WHERE ";
            $x    = 0;
            foreach ($params as $key => $value) {
                if (!is_array($value)) {
                    // just a basic query
                     
                    $sql .= "`".$key."` = '" .$value . "'";
                } else {
                    if (!is_array($value['value'])) {
                        // can pass operators now 
                        // i.e. $params = array('type_id' => array('operator' => '>', 'value' => 3));
                        $sql .= "`".$key."` " .$value['operator']. " '" .$value['value'] . "'";
                    } else {
                        $values = implode(',', $value['value']);
                        // where the value is an array
                        $sql .= "`".$key."` IN (" .$values . ")";
                    }
                }
                $x++;
                if ($x != count($params)) {
                   $sql .= " AND "; 
                }
            }
             
        }

        $result = $this->_db_connection->query($sql);

        $result = $this->query($sql);
        $row    = $result->fetch_assoc();
        return $row['count'];

    }

    /**
     * Parse value for the sql query
     * 
     * @param string $value Value to be parsed
     * 
     * @return string
     */
    public function parseValue($value)
    {
        if($value === "NULL")
        {
            return $value;
        }
        return "'". $value ."'";
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
        $sql = "SELECT ";

        // selecting columns we're going to use
        if (is_array($columns)) {
            $sql .= implode(",", $columns);
        } else {
            $sql .= $columns;
        }

        $sql .= " FROM `". $this->_table ."`";

        if (!empty($joins)) {
            foreach ($joins as $join) {
                $sql.= " LEFT JOIN `".$join['table']."` ON `"
                    . $this->_table ."`.".$join['on']." = `".$join['table']."`.".$join['on2'];
            }
        }

        // setting up WHERE values
        if (null !== $params) {
            $sql .= " WHERE ";
            $sql .= $this->_paramsArrayToWhereStatement($params);
        }
        
        //setting up GROUP BY
        if (null !== $group_by) {
            $sql .= " GROUP BY ".$group_by;
        }

        // setting up HAVING values
        if (null !== $having) {
            $sql .= " HAVING ";
            $x    = 0;
            foreach ($having as $key => $value) {
                if (!is_array($value)) {
                    // just a basic query
                     
                    $sql .= "`".$key."` = '" .$value . "'";
                } else {
                    if (!is_array($value['value'])) {
                        // can pass operators now 
                        // i.e. $params = array('type_id' => array('operator' => '>', 'value' => 3));
                        $sql .= "`".$key."` " .$value['operator']. " '" .$value['value'] . "'";
                    } else {
                        $values = implode(',', $value['value']);
                        // where the value is an array
                        $sql .= "`".$key."` IN (" .$values . ")";
                    }
                }
                $x++;
                if ($x != count($params)) {
                   $sql .= " AND "; 
                } 
            }
             
        }

        // setting up ORDER
        if (null !== $order) {
            if (preg_match("/DESC/", $order, $matches)) {
                $order = explode("DESC", $order);
                $sql .= " ORDER BY `". $this->_table ."`.`".trim($order[0])."` DESC";
            } else if(preg_match("/FIND_IN_SET/", $order)) {
                $sql .= " ORDER BY ".$order."";
            } else {
                $sql .= " ORDER BY `". $this->_table ."`.`".$order."`";
            }
             
        }

        // setting up LIMIT
        $sql .= $this->_parseLimitToSQL($limit, $page);
        // run query
        $result = $this->query($sql);
        $this->_parseRowset($result);
        return $this;
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

    protected function _paramsArrayToWhereStatement($params = null)
    {
        $sql = '';
        
        if($params == null)
        {
            return null;
        }
        $x    = 0;
        foreach ($params as $key => $value)
        {
            
            // if the key has a dot in it, then use the left handside as the table
            // if not use $this->_table as the table
            if(strpos($key, '.') === false)
            {
                $table = $this->_table;
            }
            else
            {
                $data = explode('.', $key);
                $table = $data[0];
                $key = $data[1];
            }
            
            // parse the value into safe sql
            if (!is_array($value))
            {
                $sql .= "`". $table ."`.`".$key."` = " .$this->parseValue($value);
            }
            
            // if the value is an array then this query contains operators
            else
            {
                if (!is_array($value['value']))
                {
                    // can pass operators now i.e. 
                    // $params = array('type_id' => array('operator' => '>', 'value' => 3));
                    if(!is_null($value['value']))
                    {
                        $sql .= "`". $table ."`.`".$key."` " 
                             .$value['operator']. " " 
                             .$this->parseValue($value['value']);
                    }
                    
                    // if value is null
                    else
                    {
                        if ($value['operator'] == '=')
                        {
                            $sql .= "`".$key."` IS NULL";
                        }
                        else
                        {
                            $sql .= "`".$key."` IS NOT NULL";
                        }
                    }                         
                }
                else
                {
                    $values = implode(',', $value['value']);
                    // where the value is an array
                    $sql .= "`". $table ."`.`".$key."` IN (" .$values . ")";
                }
            }
            $x++;
            if ($x != count($params)) {
                $sql .= " AND ";
            }
        }
        return $sql;
    }
    
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
        $sql = "SELECT * FROM `". $this->_table ."`";

        $values = implode(',', $ids);
        $sql .= " WHERE `". $this->_table ."`.`id` IN (" .$values . ")";

        $sql .= " ORDER BY FIELD(`id`, ". $values .") ";
        // setting up LIMIT
        if (null !== $limit) {
            if (null !== $page) {
                $sql .= " LIMIT ".$limit * ($page - 1) . ",". $limit;
            } else {
                $sql .= " LIMIT ".$limit;
            }
        }

        // run query
        $result = $this->query($sql);
        $this->_parseRowset($result);
        return $this;
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
     * @return Vanilla_MySQL
     */

    public function getByField($field_name, $field_value)
    {
        $sql = "SELECT * FROM `". $this->_table ."` "
        ." WHERE `" . $field_name . "` = \"" . $this->_db_connection->real_escape_string($field_value)
        . "\" LIMIT 1";
        $result = $this->query($sql);
        $this->_parseRow($result);
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
         $sql ="SELECT MAX(`order`)+1 as `newOrder` FROM `".$this->_table
        ."` WHERE `status` = ".Vanilla_Model_Row::STATUS_LIVE;
        
        $result = $this->query($sql);
        $this->_parseRow($result);

        if($this->_data[0]['newOrder'] == 0){
            return 1;
        }
        else
        {
            return $this->_data[0]['newOrder'];
        }
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

    /**
     * Delete using parameters
     * Example parameters:
     * $params = array('type_id' => array('operator' => '>', 'value' => 3)
     *
     * @param array $params Parameters
     * 
     * @return bool
     */

    protected function deleteWhere(array $params)
    {
        $sql = "DELETE FROM `". $this->_table ."` WHERE ";

        // setting up WHERE values
        $x = 0;
        foreach ($params as $key => $value) {
            if (!is_array($value)) {
                // just a basic query
                $sql .= "`".$key."` = '" .$value . "'";
            } else {
                // can pass operators now i.e. $params = array('type_id' => array('operator' => '>', 'value' => 3));
                $sql .= "`".$key."` " .$value['operator']. " '" .$value['value'] . "'";
            }
            $x++;
            if ($x != count($params)) {
                $sql .= " AND ";
            } 
        }

        return $this->query($sql);
    }

}
