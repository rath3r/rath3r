<?php
/**
 * Router
 *
 * @name     Vanilla Router
 * @category Router
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Request
 *
 * @name     Vanilla Router
 * @category Router
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */


class Vanilla_Router
{
    /**
     * Requested url
     * @var string
     */
    public $url;

    /**
     * Action selected through routes
     * @var string
     */
    public $action;

    /**
     * Controller selected through routes
     * @var string
     */
    public $controller;

    /**
     * Module selected through routes
     * @var string
     */
    public $module;

    /**
     * Page record selected through routes
     * This can be empty if using file routes
     * @var Pages_Model_Page
     */
    public $page;

    /**
     * Access type user id
     * @var int
     */
    public $users_type_id;

    /**
     * Specify the name of Error 404 Controller
     */
    const ERROR_CONTROLLER = "Controller_Error";

    /**
     * Specify the name of Error 404 Action
     */
    const ERROR_ACTION     = "error404Action";

    /**
     * Specify the name of Error 500 Action
     */
    const INTERNAL_ERROR_ACTION     = "error500Action";

    /**
     * Specify the name of Error 401 Action
     */
    const AUTH_CONTROLLER = "Controller_Home";

    /**
     * Specify the name of Error 401 Action
     */
    const AUTH_ACTION     = "loginAction";


    /**
     * Specify the name of Error 401 Action
     */
    const AUTH_PASSWORD_CHANGE_ACTION     = "changePasswordAction";


    /**
     * Relative path to the directory with all the routes
     */
    const ROUTES_DIR       = "conf/routes/";

    /**
     * Function that does all the routing work
     * 
     * @return void
     */

    public function route()
    {
    	// assign $this->url
        $this->_setUrl();
        //$this->_checkLanguage();
        if (!$this->_findRouteFromFile()) {
            if (!$this->_findPageByUrl()) {
                $this->_checkRedirect();
            }
        }
        
        $this->_checkRouting();
        $this->_checkACL();
    }

    private function _checkLanguage()
    {
        if(class_exists("Languages_Model_Language"))
        {
            $_tmp = explode("/", $this->url['path']);
            $_tmp = array_filter($_tmp, 'strlen');
            if(strlen($_tmp[1]) == "2")
            {
                $_GET['language_iso'] = $_tmp[1];
            }
        }
    }
    
    /**
     * Loading routes from files first
     * 
     * @param array $route_files Route Files array
     * 
     * @return boolean
     */

    protected function _findRouteFromFile($route_files = null)
    {
        $routes = self::getAllRoutesAsArray();
        //var_dump($routes);
        $correct_path = $this->getCorrectPath();
        
        foreach ($routes as $route_name => $route) {
            $pattern  = "/^" . str_replace(array("/"), array("\/"), $route['pattern']) . "/";
            
            if (preg_match($pattern, $correct_path, $matches)) 
            {
                if(isset($matches['controller'])) 
                {
                    $controller = ucwords(strtolower($matches['controller']));
                }
                else
                {
                     $controller =  $route['controller'];
                }
                $this->controller = "Controller_" . $controller;
                $action           = isset($matches['action']) ? $matches['action'] : $route['action'];
                $this->action     = $action . "Action";
                if(Vanilla_Module::isInstalled("Pages"))
                {
                    $this->page       = Model_Page::factory()->createFromRoute($route, $route_name);
                }
                $this->module     = isset($route['module']) ? $route['module'] : null;
                $this->assignVariables($route);
                if (null !== $this->module) {
                    $this->controller = $this->module . "_" . $this->controller;
                }
                $this->_assignToHTTPVariables($matches);
                unset($routes);
                return true;
            }
        }
        unset($routes);
        return false;
    }

    /**
     * Assign variable passed in routes to the REQUEST request
     * 
     * @param array $route Route array
     * 
     * @return void
     */
    public function assignVariables($route)
    {
        if (isset($route['var'])) {
            foreach ($route['var'] as $key => $value) {
                $_GET[$key] = $value;
            }
        }
    }

    /**
     * Assigns array values to the HTTP Variables Array
     * Will only accept values with $key being non numeric
     * 
     * @param Array  $matches Matches
     * @param string $type    Type
     * 
     * @return void
     */

    private function _assignToHTTPVariables($matches, $type = "GET")
    {
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                switch($type)
                {
                    case "GET":
                        $_GET[ $key] = $value;
                        break;
                    case "POST":
                        $_POST['$key'] = $value;
                        break;
                }
            }
        }
    }

    /**
     * Get route by route_id
     * 
     * @param string $route_id
     * 
     * @return array
     */
    public function getRoute($route_id)
    {
        $routes = self::getAllRoutesAsArray();
        if(isset($routes[$route_id]))
        {
            return $routes[$route_id];
        }
        return array();
    }

    /**
     * Setting the URL variable
     * 
     * @return null
     */

    protected function _setUrl()
    {
        $http = Vanilla_Url::isSSL() ? 'https://':  'http://';
        $url = $http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->url = parse_url($url);
    }
    
    public function getCorrectPath()
    {
        if(Vanilla_Module::isInstalled("Languages"))
        {
            $locale   = Vanilla_Locale::getFromSession();
            $_tmp     = explode("/", $this->url['path']);
            $language = $locale->getLanguage();
            if($language !== null && strtolower($_tmp[1]) == $language)
            {
                //unset($_tmp[1]);
            }
            $path = implode("/", $_tmp);
            return $path;
        }
        else 
        {
            return $this->url['path'];
        }
        
    }
    
	/**
     * Getting all file routes from the files
     * 
     * @return array
     */
    public static function getAllFileRoutesAsPages()
    {
        $routes_dir = getcwd() . "/". LIB_APP_DIR . self::ROUTES_DIR;
        $handle     = opendir($routes_dir);
        $routes     = array();
        while (false !== ($file = readdir($handle))) 
        {
            if (substr($file, 0, 1) != ".")
            {
                $data   = parse_ini_file($routes_dir. $file, true);
                foreach ($data as $key => $route)
                {
                    $routes[] = Model_Page::factory()->createFromRoute($route, $key);
                }
            }
        }
        return $routes;
    }

    /**
     * Find correct Page by URL in the database
     * 
     * @return boolean
     */

    protected function _findPageByUrl()
    {
        try
        {
            $pages = new Model_Pages();
            $page = $pages->findByUrl($this->getCorrectPath());
            
            unset($pages);
            
            if(!$page)
            {
                $this->_setAs404("Page for url " .$this->url['path']. " not created");
                return false;
            }
            else
            {
                $acl  = new Vanilla_ACL();
                $user = $acl->getLoggedUser();
                if($page->status == Model_Pages::STATUS_LIVE || $user->isAdmin())
                {
                    $this->controller    = "Controller_" . trim($page->controller);
                    $this->action        = trim($page->action) . "Action";
                    $this->page          = $page;
                    $this->users_type_id = isset($page->users_type_id) ? $page->users_type_id : 2;
                    return true;
                } else {
                    $this->_setAs404("Page with id=".$page->id." is not live.");
                    return false;

                }
            }
        }
        catch (Vanilla_Exception_MySQL $e)
        {
            $this->_setAs500($e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * Check if the URL has been changed and a redirect is needed
     * 
     * @todo write all the functionality
     * 
     * @return void
     */

    protected function _checkRedirect()
    {
        
    }

    /**
     * Check If Routing was a success
     * 
     * @return boolean
     */

    protected function _checkRouting()
    {
        $this->_ifNullAssignDefaults();
        $path = explode(PATH_SEPARATOR, get_include_path());
        foreach ($path as $dir) {
            $controller_path = $dir . parse_class_name_into_dir($this->controller). ".php";
            if (file_exists($controller_path)) {
                if (method_exists($this->controller, $this->action)) {
                    return true;
                } else {
                    $this->_setAs404(
                        "Action "
                        .$this->action
                        ." missing in file "
                        .parse_class_name_into_dir($this->controller)
                    );
                    return false;
                }
            }
        }
        
        $this->_setAs404("Controller file ". parse_class_name_into_dir($this->controller) . " Missing");
        return false;
    }

    /**
     * If no values set, assign Defaults
     * For pages that do not have a Controller or action specified
     * Assign default values
     * 
     * @return void
     */

    private function _ifNullAssignDefaults()
    {
        // Assigning default if none defined
        if ($this->controller === null) {
            $this->controller = "Vanilla_Controller_Default";
        }

        if ($this->action === null) {
            $this->action = "index";
        }
    }

    /**
     * Checking ACL
     * 
     * @return void
     */
    
    protected function _checkACL()
    {
        if ($this->controller != self::ERROR_CONTROLLER)
        {
            $acl = new Vanilla_ACL();
            $response = $acl->hasPermission($this->page, $this->module);
        if ($response === Vanilla_ACL::RESPONSE_404)
            {
                $this->_setAs404("Page doesn't exist");
            }

            if ($response === Vanilla_ACL::RESPONSE_401)
            {
                $this->_setAs401("Needs Authorisation");
            }

            if ($response === Vanilla_ACL::RESPONSE_202)
            {
                $this->_setAs202("Password Change");
            }
        }
    }

    /**
     * Read All Route files and return them as array
     * 
     * @return array
     */
    public static function getAllRoutesAsArray()
    {
        $routes     = array();
        
        $routes_dir = getcwd() . "/". LIB_APP_DIR . Vanilla_Router::ROUTES_DIR;
        $handle = opendir($routes_dir);
        while (false !== ($file = readdir($handle))) {
            if (substr($file, 0, 1) != ".") {
                $data     = parse_ini_file($routes_dir. $file, true, INI_SCANNER_RAW);
                $routes  += $data;
            }
        }
        return $routes;
    }

    /**
     * Setting the Page as 404
     * Request not found
     * 
     * @param string $error_message Error Messages
     * 
     * @return void
     */

    private function _setAs404($error_message)
    {
        Vanilla_Debug::Error($error_message, false);
        $this->controller = self::ERROR_CONTROLLER;
        $this->action     = self::ERROR_ACTION;
        $this->module     = null;
    }

    /**
     * Setting the Page as 500
     * Internal server error
     * 
     * @param string $error_message Error Messages
     * 
     * @return void
     */
    private function _setAs500($error_message)
    {
        Vanilla_Debug::Error($error_message, false);
        $this->controller = self::ERROR_CONTROLLER;
        $this->action     = self::INTERNAL_ERROR_ACTION;
        $this->module     = null;
    }

    /**
     * Setting the Page as 401
     * Authorisation required
     * 
     * @param string $error_message Error Messages
     * 
     * @return void
     */

    private function _setAs401($error_message)
    {
        //header("HTTP/1.1 401 Authorization Required");
        Vanilla_Debug::Error($error_message, false);
        $this->controller = self::AUTH_CONTROLLER;
        $this->action     = self::AUTH_ACTION;
        $this->module     = null;
    }

    /**
     * Setting the Page as 202
     * Password change required
     * 
     * @param string $error_message Error Messages
     * 
     * @return void
     */

    private function _setAs202($error_message)
    {
        //header("HTTP/1.1 401 Authorization Required");
        Vanilla_Debug::Error($error_message, false);
        $this->controller = self::AUTH_CONTROLLER;
        $this->action     = self::AUTH_PASSWORD_CHANGE_ACTION;
        $this->module     = null;
    }
}
