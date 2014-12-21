<?php
/**
 * PHP Version 5.3.5
 * Bootstrapping the autoload
 * This will automatically include required functions
 *
 * @category Bootstrap
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://192.168.50.14/vanilla-doc/
 *
 */

session_start();

require_once 'Config/Ini.php';

require_once 'functions.php';

require_once 'Debug.php';

spl_autoload_extensions('.php');

register_shutdown_function('handleShutdown');

function handleShutdown()
{
    $error = error_get_last();
    if($error !== NULL && !in_array($error['type'], array(8)))
    {
        $info   = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;
        $tags[] = Vanilla_Debug::errorTypeToName($error['type']); 
        Vanilla_Debug_Amon::log($info, $tags);
        $dmp = debug_backtrace();
    }
}

/**
 * PHP Version 5.3.5
 * Bootstrapping the autoload
 * This will automatically include required functions
 *
 * @param string $class_name Name of the class we will be loading
 *
 * @return  void
 */

function _autoloadVanilla($class_name)
{
    $class_name = parse_class_name_into_dir($class_name);
    $class_file = $class_name . '.php';
    if(stream_resolve_include_path($class_file) !== false)
    {
        include_once $class_file; 
    }
}

/**
 * PHP Version 5.3.5
 *
 * @name    Vanilla_Bootstrap
 * @category Bootstrap
 *
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://192.168.50.14/vanilla-doc/
 *
 */

class Vanilla_Bootstrap
{
    /**
     * All the configuration data will be stored here
     * @var array
     */
    public $config;

    /**
     * Router class
     * @var Vanilla_Router
     */
    public $router;

    /**
     * Smarty class
     * @var Smarty
     */
    public $smarty;

    /**
     * Destruct function
     * Will just show the memory usage
     *
     * @return void
     */
    public function __destruct()
    {
        Vanilla_Debug::getMemoryUsage();
    }

    /**
     * Default run function
     * Initiates the Application
     *
     * @return void
     */

    public function run()
    {
        $this->_removeHeaders();
        $this->_initErrors();
        $this->_initLocale();
        $this->initConfig();
        $this->_initEncoding();
        $this->_initPaths();
        Vanilla_Debug::getMemoryUsage();
        $this->_initSmarty();

        spl_autoload_register('_autoloadVanilla');
        $this->_initRouter();
        $this->_initController();
    }

    public function _removeHeaders()
    {
        header_remove('X-Powered-By');
        header_remove('Server');
    }

    public function _initErrors()
    {
        if(APPLICATION_ENVIRONMENT == "development")
        {
            ini_set('error_reporting', E_ALL);
        }
    }
    
    /**
     * Initializing the Encoding set in APP_CHARSET
     *
     * @return void
     */

    protected function _initLocale()
    {
        date_default_timezone_set('Europe/London');

    }

    /**
     * Initiate Configuration file
     *
     * @return void
     */

    public function initConfig()
    {
        try {
            $this->config = new Vanilla_Config_Ini();
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        $this->_parseConfigFile();
    }



    /**
     * Initializing the Encoding set in APP_CHARSET
     *
     * @return void
     */

    protected function _initEncoding()
    {
        header('content-type: text/html; charset: '.strtolower(APP_ENCODING));
        mb_internal_encoding(APP_ENCODING);
    }

    /**
     * Parse config file
     * Assign settings to constants
     *
     * @return void
     */

    private function _parseConfigFile()
    {
        foreach ($this->config as $namespace => $properties) {
            //specifying which variables I don't want to keep as constants
            if (!in_array($namespace, array('router'))) {
                foreach ($properties as $key => $value) {
                    if (!is_array($value)) {
                        $constant_name = parse_constant_name($namespace . "_" . $key);
                        if (!defined($constant_name)) {
                            define($constant_name, $value); 
                        }
                    }
                }
            }
        }

        $this->_getBaseURL();
    }

    /**
     * Sets the BASE URL global with the url
     * 
     * @todo assess if it's http or https
     * @return void
     */
    private function _getBaseURL()
    {
        $base_url = "http://" . $_SERVER['SERVER_NAME'];
        define("BASE_URL", $base_url);
    }

    /**
     * Including paths to the libraries
     * 
     * @return void
     */

    protected function _initPaths()
    {
        $path = '';
        if (!defined('LIB_BASE_DIR')) {
            define('LIB_BASE_DIR', getcwd());
        }
        
        foreach ($this->config->lib as $namespace => $lib_path) {
            if(substr($lib_path, 0, 1) != "/")
            {
                $path .= PATH_SEPARATOR . LIB_BASE_DIR . DIRECTORY_SEPARATOR . trim($lib_path);
            }
        }
        
        $path .= PATH_SEPARATOR . get_include_path();
        
        
        set_include_path($path);
    }

    /**
     * Initialize Smarty
     * We're using Smarty as the templating system, so all the set up happens here
     * If debug is enabled, we're switching smarty debug on
     * 
     * @return void
     */

    protected function _initSmarty()
    {
        if (isset($this->config->smarty)) {
            include_once(BASE_DIR . LIB_FRAMEWORK_DIR . "Vanilla/Output/Smarty.php");
            $this->smarty = new Vanilla_Output_Smarty();
            $this->smarty->init($this->config->smarty);
        }
    }

    /**
     * Initializing Router
     * Getting all the configuration from the config files
     * Router is responsible for selecting the correct Controller and Action
     * It also connects with the ACL class and verifies the user/guest have
     * permission to view the page they have asked for
     * 
     * @return void
     */
    protected function _initRouter()
    {
        $this->router = new Vanilla_Router();
        $this->router->route();
	}

    /**
     * Initilaizng Controller decided upon in the Router
     * Setting up Controller with all the variables
     * If the controller set up returns errors, we're
     * throwing an exception that will redirect the user
     * to correct error message
     * 
     * @return void
     */

    protected function _initController()
    {
        try
        {
            $this->_runController(
                $this->router->controller, 
                $this->router->action, 
                $this->router->module, 
                $this->router->page
            );
        }
        catch (Vanilla_Exception_Route $e)
        {
            $this->_runController("Controller_Error", $e->getAction(), null, null, $e->getMessage());
            Vanilla_Debug::Error($e->getMessage());
             
        }
        unset($this->router);
    }

    /**
     * This funciton runs the Controller and assigns all variables
     * 
     * @param string             $controller_name pass controller name
     * @param string             $action          pass action name
     * @param string             $module          pass module name
     * @param Pages_Model_Page 	 $page            pass page object
     * @param string             $error_message   pass error message if needed.
     *
     * @return void
     */

    private function _runController($controller_name, $action, $module = null, $page = null, $error_message = null)
    {
        $controller = new $controller_name($controller_name, $action, $module, $page);
        $controller->smarty = $this->smarty;
        if (null !== $error_message) {
            $controller->addSystemMessage($error_message);
        }
        $controller->$action();
    }


}
