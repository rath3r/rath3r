<?php
/**
 * Modules operator
 *
 * @name     Vanilla_Modules
 * @category Modules
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.3
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Modules operator
 *
 * @name     Vanilla_Modules
 * @category Modules
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.3
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Module
{

    /**
     * Checking whether the module is installed
     * Will require for class $ModuleName_Module to be exist
     * e.g. Languages_Module or Surveys_Module
     * 
     * @param unknown_type $module
     * 
     * @return boolean
     */
    public static function isInstalled($module)
    {
        return class_exists($module."_Module");
    }

} // End Modules
