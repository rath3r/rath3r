<?php

/**
 * Vanilla Config File Loader
 *
 * @name       Vanilla Config Ini
 * @category   Config
 * @package    Vanilla
 * @subpackage Config
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

require_once "Config.php";

/**
 * Vanilla Config File Loader
 *
 * @name       Vanilla Config Ini
 * @category   Widget
 * @package    Vanilla
 * @subpackage Config
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Config_Ini extends Vanilla_Config
{

    /**
     * Parsing the ini file
     * Extends basic parse_ini_file functionality
     * Adds redundancy for environemnts etc
     * 
     * @return $config Vanilla_Config_Ini
     */

    public function __construct()
    {
        $this->_loadEnvironments();
        return $this;
    }

    /**
     * Load the production ini which is the main ini
     * 
     * @chainable
     * 
     * @return Vanilla_Config_Ini
     */
    private function _loadProduction()
    {
        $config_file = PATH_CONFIG . "production.ini";
        return $this->parseFile($config_file);
    }

    /**
     * Load the config file for staging which will overwrite production setup
     * 
     * @chainable
     * 
     * @return Vanilla_Config_Ini
     */
    private function _loadStaging()
    {
        $config_file = PATH_CONFIG . "staging.ini";
        return $this->parseFile($config_file);
    }

    /**
     * Load the config file for development which will overwrite production setup
     * 
     * @chainable
     * 
     * @return Vanilla_Config_Ini
     */
    private function _loadDevelopment()
    {
        if (empty($_ENV['USER']) && empty($_SERVER['USER'])) {
            throw new Exception(
                "There doesn't seem to be an environment variable set, you might 
                want to overwrite this setting with a default dev conf file"
            );
            die;
        }
        
        if (!empty($_ENV['USER'])) {
            $config_file = PATH_CONFIG . $_ENV['USER'] . ".ini";
        } else {
            $config_file = PATH_CONFIG . $_SERVER['USER'] . ".ini";
        }
        return $this->parseFile($config_file);
    }

    /**
     * Loads Config Files based on Environment
     * Specified in vhosts conf file SetEnv APPLICATION ENVIRONMENT
     * Will load production first, then if we're not in production Environment
     * overwrite it with Staging, and if we're not in Staging
     * overwrite it with Developer's config
     * 
     * @return Vanilla_Config_Ini
     */
    
    private function _loadEnvironments()
    {
        // always load the production settings
        $this->_loadProduction();
        if (APPLICATION_ENVIRONMENT != "production") {
            $this->_loadStaging();
            if (APPLICATION_ENVIRONMENT != "staging") {
                $this->_loadDevelopment();
            }
        }
        return $this;
    }
}