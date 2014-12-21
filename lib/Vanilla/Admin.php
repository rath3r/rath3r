<?php

/**
 * Vanilla_Admin
 * Class that will control the Vanilla Admin behaviour
 * 
 * @name     Vanilla_Admin
 * @category Class
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 * @todo     build the whole functionality for this Vanilla Admin / Vanilla Lib / Application bridge
 */

/**
 * Vanilla_Admin
 * Class that will control the Vanilla Admin behaviour
 * 
 * @name     Vanilla_Admin
 * @category Class
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 * @todo     build the whole functionality for this Vanilla Admin / Vanilla Lib / Application bridge
 */

class Vanilla_Admin
{
    /**
     * Store config here
     * @var Vanilla_Config
     */
    public $config;
    
    /**
     * Config File
     * @var string
     */
    public static $config_file = "admin.conf";
    
    /**
     * Remap modules
     * @var array $_remap_modules
     */
    public static $_remap_modules = array('file_groups' => 'files', 'persons' => 'people', 'videos' => 'media', 'images' => 'media', 'audios' => 'media', 'carousel_items' => 'files');
    
    /**
     * Refactored Modules
     * @todo get rid of this when all modules refactored
     * @var  array $_refactored_modules
     */
    private static $_refactored_modules = array('files', 'users', 'pages', 'events', 'people', 'media', 'widgets', 'themes');

    /**
     * Factory class for static initialization
     * 
     * @return Vanilla_Admin
     */
    public static function factory()
    {
        $object = new Vanilla_Admin();
        return $object;
    }

    /**
     * Parsing the config file for the admin
     * 
     * @return Vanilla_Config
     */
    public function loadConfig()
    {
        if ($this->config === null) {
            $config_file  = PATH_CONFIG . self::$config_file;
            $this->config = Vanilla_Config::factory()->parseFile($config_file);
        }
        return $this->config;
    }

    /**
     * Checking if Entity has related entities
     * 
     * @param string $entity Entity name
     * 
     * @return boolean
     */
    public function hasRelated($entity)
    {
        $entity = strtolower($entity);
        $has_relate = 0;
        $this->loadConfig();
        if (isset($this->config->$entity)) {
            foreach ($this->config->$entity as $key => $value) {
                if ($this->isRelationshipAssignKey($key)) {
                    if ($value == true or $value == "on") {
                        $has_related++;
                    }
                }

            }
        }
        if ($has_related > 0) {
            return true;
        }
        return false;
    }

    /**
     * Checking if key is a relationship assign variable
     * 
     * @param string $key Key name
     * 
     * @return boolean
     */    
    public function isRelationshipAssignKey($key)
    {
        if (preg_match("/^assign\_(.)+/", $key)) {
            return true;
        }
        return false;
    }

    /**
     * Initializing object for the assigned module
     * 
     * @param string       $module Module passed
     * @param (Row|Rowset) $type   Are we initiating a Row Object or a Rowset
     * 
     * @example Vanilla_Admin::initAssignedModuleObject("keywords") will return Vanilla_Model_Keywords
     * 
     * @return Vanilla
     */
    public static function initAssignedModuleObject($module, $type = "Rowset")
    {
        if (!empty($module)) {
            $class_name = self::getClassName($module);
            if (class_exists($class_name)) {
                $object      = new $class_name();

                if ($type == "Row") {
                    return $object->getRowObject();
                }

                return $object;
            }
        }
        return null;
    }

    /**
     * Get Class Name
     * 
     * @param string $module Module name
     * 
     * @return string
     */
    private static function getClassName($module) 
    {

        $part_class_name = $module;
        $part_class_name  = ucwords(str_replace("_", " ", $part_class_name));
        $part_class_name  = str_replace(" ", "_", $part_class_name);

        if (array_key_exists($module, self::$_remap_modules)) {
            $module = self::$_remap_modules[$module];
        }

        if (in_array($module, self::$_refactored_modules)) {
            $class_name  = ucwords($module)."_Model_".$part_class_name;
        } else {
            $class_name  = "Vanilla_Model_".$part_class_name;
        }

        return $class_name;
    }

    /**
     * Checking if section option has specific value
     * 
     * @param string $section Name of section
     * @param string $option  Which option are we checking
     * @param string $value   Value name
     * 
     * @return boolean
     */
    public function sectionOptionHasValue($section, $option, $value)
    {
        $this->loadConfig();
        if ($this->config->$section->$option == $value) {
            return true;
        }
        return false;
    }

    /**
     * Removing the assingned_ bit from the conf switch on off bit
     * 
     * @param string $conf_string Config string we're selected
     * 
     * @example Vanilla_Admin::getModuleNameFromConf("assign_file_group") returns file_group
     * 
     * @return string
     */
    public static function getModuleNameFromConf($conf_string)
    {
        $module = explode("_", $conf_string);
        if ($module[0] == "assign") {
            // removing assign from the first part
            unset($module[0]);
            $module  = implode("_", $module);
            return $module;
        }
        return null;
    }
}


