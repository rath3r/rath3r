<?php
/**
 * Vanilla Config
 * Handles All the ini configuration files
 *
 * @name     Vanilla_Config
 * @category Config
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla Config
 * Handles All the ini configuration files
 *
 * @name     Vanilla_Config
 * @category Config
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Config
{
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

    /**
     * Parse config file, assign values
     * 
     * @param string $filename Filename string
     * 
     * @throws Exception
     * @chainable
     * 
     * @return Vanilla_Config
     */
    public function parseFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception("Environment config: ". $filename . " is missing. Please add it.");
            die;
        }

        $p_ini = parse_ini_file($filename, true);
        foreach ($p_ini as $namespace => $properties) {
            foreach ($properties as $name => $value) {
                $this->$namespace->$name = $value;
            }
        }
        return $this;
    }

    /**
     * Turn config to array
     * 
     * @return array
     */

    public function toArray()
    {
        return get_object_vars($this);
    }
}