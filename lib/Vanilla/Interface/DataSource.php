<?php

/**
 * Vanilla_Interface_DataSource
 *
 * @name       Vanilla_Interface_DataSource
 * @category   Interface
 * @package    Vanilla
 * @subpackage Interface
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Interface_DataSource
 *
 * @name       Vanilla_Interface_DataSource
 * @category   Interface
 * @package    Vanilla
 * @subpackage Interface
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */


interface Vanilla_Interface_DataSource
{
    /**
     * Create new record for data
     * 
     * @param array   $data   Data Array for creation
     * @param boolean $backup Are we adding backup?
     * 
     * @return Vanilla_Model_Row
     */
    public function create($data, $backup = false);

    /**
     * Get Data Array
     * 
     * @return array
     */
    public function getData();

    /**
     * Delete record with specified id
     * 
     * @param int $id ID
     * 
     * @return void
     */
    public function delete($id);

    /**
     * Backup array
     * 
     * @param array $data Data Array for backup
     * 
     * @return void
     */
    public function backup($data);

    /**
     * Update record with new $data
     * 
     * @param array $data Data Array
     * @param int   $id   ID
     * 
     * @return Vanilla_Model_Row
     */
    public function update($data, $id);

    /**
     * Get count of records
     * 
     * @param array $params Array of Params
     * 
     * @return int
     */
    public function getCount($params);

    /**
     * Get All records matching the statement below
     * 
     * @param int    $limit    Limit
     * @param int    $page     Page
     * @param array  $params   Params
     * @param int    $order    Order
     * @param string $group_by Group By
     * @param string $columns  Columns
     * 
     * @return Vanilla_Model_Rowset
     */
    public function getAll(
        $limit = null, 
        $page = null, 
        $params = null, 
        $order = null, 
        $group_by = null, 
        $columns = "*"
    );

    /**
     * Get record by id
     * 
     * @param int $id ID
     * 
     * @return Vanilla_Model_Row
     */
    public function getById($id);

    /**
     * Find records for relationship
     * 
     * @param string $query Query
     * @param int    $limit Limit
     * 
     * @return Vanilla_Model_Row
     */
    public function findForRelationship($query, $limit);
    
    /**
	 * Find related entity label field
	 * 
	 * @return void
	 */
     public function getRelatedEntityLabelField();

}