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

abstract class Vanilla_DataSource implements Vanilla_Interface_DataSource
{
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
     * Get value of a field
     * 
     * @param string $field Field
     * 
     * @return mixed
     */
    final public function get($field)
    {
        if(isset($this->_data[0][$field]))
        {
            return $this->_data[$field];
        }
        return false;
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

    protected function _parseRow(array $row)
    {
        $this->_data[] = $row;
        return $this;
    }

}
