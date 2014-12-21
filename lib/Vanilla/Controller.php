<?php

/**
 * Basic Controller Functionality. Every controller used in the framework should
 * extend this controller.
 *
 * @name     Vanilla_Controller
 * @category Controller
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 * @abstract
 */

/**
 * Basic Controller Functionality. Every controller used in the framework should
 * extend this controller.
 * 
 * @name     Vanilla_Controller
 * @category Controller
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 * @abstract
 */

abstract class Vanilla_Controller
{
    /**
     * Instance of smarty class
     * @var Smarty
     */
    public $smarty;

    /**
     * Action name
     * @var string
     */
    public $action;


    /**
     * Controller
     * @var string
     */
    public $controller;

    /**
     * Track on
     * @var boolean
     */
    public $track_on;

    /**
     * Module
     * @var string
     */
    public $module;

    /**
     * Page Class if non file route selected
     * @var Pages_Model_Page
     */
    public $page;

    /**
     * Url string
     * @var string
     */
    public $url;

    /**
     * User - logged in or not
     * @var Users_Model_User
     */
    public $user;

    /**
     * Title
     * @var string
     */
    public $title;

    /**
     * Navigation
     * @var array
     */
    public $navigation;

    /**
     * Holds the request info
     * @var Vanilla_Request
     */
    public $request;

    /**
     * If not using smarty set to true
     * @var boolean
     */
    public $disable_view  = false;

    /**
     * Checking if for this controller / action we want to check if session expired
     * True by default
     * @var boolean
     */
    public $reset_session_expired = true;
    
    /**
     * If don't want debug, set to true
     * @var boolean
     */
    public $disable_debug = false;

    /**
     * Smarty template set manually by developer
     * If you want to use a different template to the default set one, set it here
     * @var string|null
     */
    public $smarty_template = null;
    

    /**
     * Smarty template set manually by developer
     * If you want to use a different template to the default set one, set it here
     * @var string|null
     */
    public $smarty_template_dir = array();

    /**
     * Blimp messages stored here. For all your success error output
     * @var array
     */
    public $messages;

    /**
     * Initiate Vanilla Output object
     * @var Vanilla_Output
     */
    public $vanilla_output;

    /**
     * CSS scripts array
     * @var array
     */
    protected $_css = array();

    /**
     * JS scripts array
     * @var array
     */
    protected $_js = array();

    /**
     * Placeholder for pagination
     * @var Vanilla_Pagination
     */
    public $pagination;

    public $pagination_limit;
    
    public $pagination_origial_limit;
    
    public $pagination_page = 1;
    
    /**
     * Placeholder for breadcrumbs
     * @var Vanilla_Breadcrumbs
     */
    public $breadcrumbs;

    public $vanillaLanguage;
    
    public $widgets;
    
    /**
     * Basic constructor
     * 
     * @param string             $controller Controller Name
     * @param string             $action     Action Name
     * @param string             $module     Module Name
     * @param Pages_Model_Page $page       Page Object
     * 
     * @magic
     * 
     * @return void
     */

    public function __construct($controller = null, $action = null, $module = null, $page = null)
    {
        $this->getAdminConf();
        $this->url        = new Vanilla_Url();
        $this->title      = APP_NAME;
        $this->controller = $controller;
        $this->action     = $action;
        
        $this->assignPage($page);
        
        if ($module !== null) {
            $this->module     = $module;
        }
        $this->messages = new Vanilla_Message_List();
        
        $this->request  = new Vanilla_Request();
        $this->_getUser();
        $this->initOutput();
        $this->getNavigation();
        $this->setUpMainTemplateDir();
        $this->preLoad();
    }

    public function checkSessionExpired()
    {
        $time_difference = null;
        if($this->user instanceof Users_Model_User && $this->user->id > 0)
        {
            if(defined('APP_SESSION_EXPIRE') === false)
            {
                throw new Vanilla_Exception("APP_SESSION_EXPIRE missing, please define in production.conf with a number of minutes, or false for no-expire");
            }
            else
            {   
                if(APP_SESSION_EXPIRE != false)
                {
                    if(isset($_SESSION['LAST_ACTIVITY']))
                    {
                        $expiry_seconds = APP_SESSION_EXPIRE * 60;
                        $time_difference     = time() - $_SESSION['LAST_ACTIVITY'];
                        if ($time_difference > $expiry_seconds) {
                            // last request was more than 30 minutes ago
                            session_unset();     // unset $_SESSION variable for the run-time 
                            session_destroy();   // destroy session data in storage
                            $this->user = new Model_User;

                        }
                    }
                    if($this->reset_session_expired === true)
                    {
                        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
                    }
                }
            }
        }
        return $time_difference;
    }
    
    /**
     * Checks if function is Ajax
     * if it is, assign smarty variable
     *
     * @return void
     */
    protected function _checkAjax()
    {
        if($this->request->isAjax())
        {
            $this->smarty->debugging = false;
        }
        $this->smarty->assign('vanillaIsAjax', $this->request->isAjax());
    }
    
    /**
     * Getting the order SQL bit for the collection
     *
     * @param string $default_order default order if needed
     *
     * @di
     * 
     * @return string
     */
    protected function _getOrder($default_order = null)
    {
        if(isset($_GET['field']))
        {
            $order = $_GET['field'];
            if(isset($_GET['order']) && $_GET['order'] == "-1")
            {
                $order .= " DESC";
            }
            
            return $order;
        }
        return $default_order;
    }
    
    /**
     * Setting up administrator controller things
     * 
     * @return void
     */
    public function setAdminSettings()
    {
        $this->smarty_template_dir = array(getcwd() . "/". SMARTY_ADMIN_TEMPLATE_DIR);
        $this->addJavascript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', 'prepend');
        $this->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js', 'prepend');
        $this->addStylesheet('/admin/css/smoothness/jquery-ui-1.8.16.custom.css', 'prepend');
        $this->addStylesheet('/admin/css/admin.css');
    }
    
    /**
     * Initiates CKEditor for all your content editing needs
     * This will add all required functions
     *
     * @return void
     */

    public function initCKEditorJavascript()
    {
        //$this->addJavascript('/admin/js/ckeditor/ckeditor.js');
        //$this->addJavascript('/admin/js/ckeditor/ckfinder/ckfinder.js');
        //$this->addJavascript('/admin/js/ckeditor/adapters/jquery.js');
    }
    
    /**
     * Add Smarty Template Directory
     * 
     * @param string $template_dir Template Directory Path
     * 
     * @return void
     */

    public function addSmartyTemplateDir($template_dir)
    {
        $this->smarty_template_dir[] = getcwd() . "/". $template_dir;
    }
    
	/**
     * Setting up for output buffering. Will allow to display output on progress.
     * 
     * @return void
     */
    public function setUpForOutputBuffering()
    {
        $this->disableView();
        @apache_setenv('no-gzip', 1); 
        @ini_set('zlib.output_compression', 0);
		ob_implicit_flush(true);
        ob_end_flush();
    }
    
    /**
     * Get User if logged in
     * 
     * @return void
     */

    private function _getUser()
    {
        $acl              = new Vanilla_ACL();
        $this->user       = $acl->getLoggedUser();
        
        if (
            $this->request->isPost() 
            && Vanilla_Module::isInstalled("Users")
            && !empty($_POST[Model_User::PASSWORD_LABEL]) 
            && !empty($_POST[Model_User::USERNAME_LABEL])
            ){
                if ($this->user->id > 0) {
                    //$this->addSuccessMessage("You are now logged in.");
                    return true;
                }else{
                    //$this->addErrorMessage("We were unable to log you in.");
                    return false;
                }
            }
    }

    /**
     * Get previous url
     * 
     * @deprecated Please use $this->request->getPreviousUrl() instead
     * 
     * @return string
     */
    public function getPreviousUrl()
    {
        return $this->request->getPreviousUrl();
    }

    /**
     * Sets the CSV headers and disables view
     * Use with any sort of CSV output
     * 
     * @param string $file_name Name of file that will be returned
     * 
     * @return void
     */
    public function displayCSVOutput($file_name)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="'.$file_name.'";' );
        header("Content-Transfer-Encoding: binary");
        
        $this->smarty->debugging = false;
    }

    /**
     * If smarty not initiated initiate output
     * 
     * @todo check if smarty is required
     * 
     * @return void
     */

    public function initOutput()
    {
        if (null === $this->vanilla_output && !isset($this->smarty)) {
            $this->vanilla_output = Vanilla_Output::init();
        }
    }
    /**
     * Check if we're doing tracking for page views.
     * 
     * @return void
     */
    private function _checkTracking()
    {
        // do tracking if switched on.
        if ($this->page instanceof Model_Page && $this->track_on && isset($this->user->id))
        {
            $this->page->track($this->user->id, "View", $this->url);
        }
    }

    /**
     * This should be always left empty on this level.
     * Can be extended in child controllers
     *
     * @return void
     */

    public function preLoad() 
    {
    }

    /**
     * Assign page to the controller.
     * Creates navigation structure from the page
     * Sets the title;
     * 
     * @param Pages_Model_Page $page Page Object
     * 
     * @return void
     */

    public function assignPage($page)
    {
        $this->page   = $page;
        if ($this->page instanceof Model_Page) {
            $this->title .= " > ".$this->page->title;
        }
    }

    /**
     * 301 Redirect (Page moved permanently)
     * Please use only for permanent redirects. For redirects after and
     * action succeeded, use 302
     *
     * @param string $new_url      Url the user will be redirected to
     * @param string $message      Message displayed
     * @param string $message_type Message Type
     *
     * @return void
     */
    public function redirect301($new_url, $message = '', $message_type = Vanilla_Message::SUCCESS)
    {
        if ($message != '') {
            Vanilla_Message::factory($message_type, $message)->saveToSession();
        }
        Header("HTTP/1.1 301 Moved Permanently");
        if(preg_match("/http/", $new_url))
        {
            Header("Location: ". $new_url);
        }
        else 
        {
            Header("Location: http://".$_SERVER['SERVER_NAME']. $new_url);
        }
        die;
    }

    /**
     * 302 Redirect (Page moved temporarily)
     * Please use only for all in-code redirects.
     *
     * @param string $new_url      Url the user will be redirected to
     * @param string $message      Message displayed
     * @param string $message_type Message Type
     *
     * @return void
     */
    public function redirect302($new_url, $message = '', $message_type = Vanilla_Message::SUCCESS)
    {
        if ($message != '') {
            Vanilla_Message::factory($message_type, $message)->saveToSession();
        }

        Header("Location: http://".$_SERVER['SERVER_NAME']. $new_url);
        die;
    }

    /**
     * Get full navigation with all child / parent dependencies
     * Assigns the navigation to $this->navigation
     *
     * @return void
     */

    public function getNavigation()
    {
        try
        {
            if (is_object($this->page) && $this->page->parent_id === "admin") 
            {
                $this->navigation = new Vanilla_Navigation('admin', $this->url->getPath());
            }
            else
            {
                $this->navigation = new Vanilla_Navigation(0, $this->url->getPath());
            }
            $this->breadcrumbs = new Vanilla_Breadcrumbs($this->navigation);
        }
        catch (Vanilla_Exception_MySQL $e)
        {
            return null;
        }
    }

    /**
     * Gets admin configuration
     */
    public function getAdminConf()
	{
		$this->admin_config = Vanilla_Admin::factory()->loadConfig();
	}
	
    
    /**
     * Add stylesheet link to the view
     * 
     * @param string $stylesheet_path full path to the css document
     * @param string $order           Order in which to add file (prepend || append)
     * 
     * @example $this->addStyleshee('/css/styles.css', 'prepend');
     * 
     * @return  void
     */

    final public function addStylesheet($stylesheet_path, $order = 'append')
    {
		$stylesheet_path = $this->_addVersioning($stylesheet_path);
		
        if(!in_array($stylesheet_path, $this->_js))
        {
            if ($order == "prepend") {
                array_unshift($this->_css, $stylesheet_path);
            } else {
                $this->_css[] = $stylesheet_path;
            } 
        }
    }

    /**
     * Remove stylesheet link to the view
     * 
     * @return  void
     */

    final public function removeStylesheet($stylesheet_path)
    {
		$stylesheet_path = $this->_addVersioning($stylesheet_path);
		foreach ($this->_css as $key => $stylesheet) {
			if ($stylesheet == $stylesheet_path) {
				unset($this->_css[$key]);
				break;
			}
		}
    }

    /**
     * Clear all stylesheet links to the view
     *
     * @return  void
     */

    final public function clearStylesheets()
    {
        $this->_css = array();
    }
        
    /**
     * Add Javascript document to the view
     * This will add javascript file to the view
     * 
     * @param string $js_file Js File To add to the queue
     * @param string $order   Order in which to add file (prepend || append)
     * 
     * @return  void
     */

    final public function addJavascript($js_file, $order = 'append')
    {
        $js_file =  $this->_addVersioning($js_file);
        
        if(!in_array($js_file, $this->_js))
        {
            if ($order == "prepend") {
                array_unshift($this->_js, $js_file);
            } else {
                $this->_js[] = $js_file;
            }
        }
    }

    /**
     * Remove javascript link to the view
     * 
     * @return  void
     */

    final public function removeJavascript($js_file)
    {
        $js_file = $this->_addVersioning($js_file);
        foreach ($this->_js as $key => $js) {
            if ($js == $js_file) {
                unset($this->_js[$key]);
                break;
            }
        }
    }

    /**
     * Clear all javascript links to the view
     *
     * @return  void
     */

    final public function clearJavascripts()
    {
        $this->_js = array();
    }
    
	/**
	 * Add versioning filename.APP_VERSION.ext from config file
	 * @param string $file_name
	 * @return string
	 */
	final private function _addVersioning($file_name)
	{
        return preg_replace('/\.(.*)$/', '.'. APP_VERSION .'.$1', $file_name);
	}
	
    /**
     * Add Error message to display in the view
     * 
     * @param string $message Message
     * 
     * @example $this->addErrorMessage("Cant't create record");
     * 
     * @return void
     */

    final public function addErrorMessage($message)
    {
        if ($message !== true) {
            $this->messages->add($message, Vanilla_Message::ERROR);
        }
    }
    
	/**
     * Add Warning message
     * 
     * @param string $message Message
     * 
     * @example $this->addWarningMessage("This page contains unrelated items");
     * 
     * @return void
     */

    final public function addWarningMessage($message)
    {
        if ($message !== true)
        {
            $this->messages->add($message, Vanilla_Message::WARNING);
        }
    }

    /**
     * Add Success message to display in view
     * 
     * @param string $message Message
     * 
     * @example $this->addSuccessMessage("Record created successfully");
     * 
     * @return void
     */

    final public function addSuccessMessage($message)
    {
        $this->messages->add($message, Vanilla_Message::SUCCESS);
    }

    /**
     * Add System message to display in view
     * 
     * @param string $message Message
     * 
     * @example $this->addSecretMessage("Record created successfully");
     * 
     * @return void
     */

    final public function addSystemMessage($message)
    {
        $this->messages->add($message, Vanilla_Message::SYSTEM);
    }


    /**
     * Checks if any error messages have been set
     * 
     * @return boolean
     */

    final public function hasErrorMessages()
    {
        return $this->messages->hasErrorMessages();
    }

    /**
     * Destruct method for the controller
     * Smarty is initiated here and all smarty variables assigned.
     * 
     * @magic
     * 
     * @return void
     */

    public function __destruct()
    {
        $this->_checkAjax();
        $this->postLoad();
        if ($this->disable_debug) {
            $this->smarty->debugging = false;
        }
        $this->_checkTracking();
        $this->setUpModuleTemplateDir();
        
        $this->smarty->setUpTemplateDirs($this->smarty_template_dir);
        $this->getWidgets();
        if (!$this->disable_view)
        {
            $this->displayViews();
        }
        if (null !== $this->vanilla_output)
        {
            echo $this->vanilla_output;
        }
        Vanilla_Debug::getMemoryUsage(" :: END ::");
    }
    
    /**
     * Checks if the object validates
     * @param Vanilla_Model_Row $object
     * @param array $data
     */
    
    public function validates(&$object, $data)
    {
        $errors = $object->validate($data);
        if(is_array($errors))
        {
            $this->addErrorMessage($errors);
            return false;
            
        }
        return true;
    }    
    public function postLoad()
    {
        $this->checkSessionExpired();
    }
    
    public function displayViews()
    {
        $template = $this->smarty->getSmartyTemplateFile(
                $this->controller, 
                $this->action,
                $this->smarty_template, 
                $this->vanillaLanguage
        );
        
        if(isset($this->smarty))
        {
        	
            $this->smarty->assign('url', $this->url->toArray());
            $this->smarty->assign('vanillaCurrentUrl', $this->url);
            $this->smarty->assign('vanillaAction', $this->action);
            $this->smarty->assign('vanillaCurrentPage', $this->page);
            $this->smarty->assign('vanillaLoggedUser', $this->user);
            $this->smarty->assign('css_files', $this->_css);
            $this->smarty->assign('js_files', $this->_js);
            $this->smarty->assign('vanillaMessages', $this->messages->fetch_all());
            $this->smarty->assign('page_title', $this->title);
            $this->smarty->assign('vanillaBreadcrumbs', $this->breadcrumbs);
            $this->smarty->setPagination($this->pagination);
            
            if(null !== $this->navigation)
            {
                $this->smarty->assign('vanillaNavigation', $this->navigation->toArray());
            }
            
            $this->getSubNavigation();
            
            if($this->request->isAjax())
            {
                $layout = "layout_modal";
            }
            
    	    
			if($this->request->isAjax())
            {
                $layout = "layout_modal";
            }
            else
            {
                $layout = "layout";
            }
            if(preg_match("/\/admin/", $this->url->getPath()))
            {
                $layout .= "_admin";
            }
            
            $this->smarty->setCompileId($layout);
            $this->smarty->assign('layout', $this->smarty->compile_id . '.tpl'); 
            // @todo make sure the cache directory is present
            $this->smarty->display($template, null, $this->smarty->compile_id);
            
        }
    }

    public function getSubNavigation()
    {
        if($this->navigation instanceof Vanilla_Navigation && $this->navigation->sub_navigation !== null)
        {
            $this->smarty->assign('vanillaSubNavigation', $this->navigation->sub_navigation);
        }
    }
    
    /**
     * Setting up the main template dir
     * Adding the general smarty path to the directories
     * 
     * @return void
     */
    public function setUpMainTemplateDir()
    {
        $this->smarty_template_dir = array(SMARTY_TEMPLATE_DIR);
    }

    /**
     * Setting up the module template dir
     * Adding the general smarty path to the directories
     * 
     * @return void
     */
    public function setUpModuleTemplateDir()
    {
        if(null !== $this->module)
        {
            $this->smarty_template_dir[] = getcwd() 
                . "/"
                .  LIB_MODULES_DIR. parse_class_name_into_dir($this->module) 
                . "/views/";
        }
    }

    /**
     * Switch off Smarty display
     * This will switch off both smarty debuging and smarty display
     * 
     * @return void
     */
    final public function disableView()
    {
        $this->disable_view  = true;
        $this->disable_debug = true;
        $this->smarty->debug = false;
    }

    /**
     * Checks if the Request to the method is an Ajax request
     * 
     * @deprecated please use $this->request->isAjax() instead
     * 
     * @return boolean
     */

    final public function isAjax()
    {
        return $this->request->isAjax();
    }

    /**
     * Use this to return an error
     * 
     * @param int    $code    HTTP Code
     * @param string $message Message
     * 
     * @throws Vanilla_Exception_Route
     * 
     * @return void
     */
    public function error($code = 404, $message = "Wrong URL")
    {
        $this->disableView();
        throw new Vanilla_Exception_Route($message, $code);
    }

    /**
     * Get all widgets that can be
     * 
     * @return void
     */
    public function getWidgets()
    {
        
        if (!empty($this->page))
        {
            if(Vanilla_Module::isInstalled("Widgets"))
            {
                if(empty($this->widgets))
                {
                    $this->widgets = Widgets_Widget_Factory::getRelatedWidgetsArray($this->page->relations, $this->smarty);
                }
                $this->smarty->assign('widgets', $this->widgets);
            }
        }
    }

	/**
     * Add widget object to the ones that will be displayed
     * 
     * @param Widgets_Widget $widget Widget Object
     * 
     * @chainable
     * 
     * @return Vanilla_Controller;
     */
    public function addWidget(Widgets_Widget $widget)
    {
        $this->widgets[] = $widget;
        return $this;
    }
    
    /**
     * Display message passed through 302 redirect.
     * 
     * @param string $redirect_url Redirect URL
     * @param string $message      Message to be displayed
     * @param string $message_type Type of message
     * 
     * @return void
     */
    public function displayMessage($redirect_url, $message, $message_type)
    {
        if(!$this->request->isAjax())
        {
            $this->redirect302($redirect_url, $message, $message_type);
        }
        else
        {
            $this->_ajaxMsg($message, $message_type);
        }
    }

    /**
     * Get all related pages/articles/events
     * 
     * @return void
     */
    protected function _getRelated()
    {

        $types    = array('Page','Article','Event');
        $related  = array();
        $count    = 0;
        $keywords = array();

        foreach ($types as $type) {
            $related[$type] = array();
        }

        foreach ($this->page->getRelated()->relations as $node) {
            if (in_array($node['db_class_name'], $types)) {
                $related[$node['db_class_name']][] = $node;
                $count++;
            } else if ($node['db_class_name'] == 'Keyword') {
                $keywords[] = $node['name'];
            }
        }

        $this->keywords = $keywords;
        $keyword_str    = count($keywords) ? implode(',', $this->keywords) : '';
        $this->smarty->assign('keywordsStr', $keyword_str);

        $related['count'] = $count;
        $this->related    = $related;
        $this->smarty->assign('related', $related);

        return $this;
    }

    /**
     * Setting pagination for the results
     * 
     * @param int $limit Limit count
     * 
     * @return void
     */
    public function setPagination($limit = 10, $total_count = null)
    {
        if(!isset($_GET['per_page']))
        {
            $this->pagination_limit = $limit;
            $this->pagination_page  = 1;
        }
        else if (isset($_GET['per_page']) && $_GET['per_page'] !== "ALL")
        {
            $this->pagination_limit = isset($_GET['per_page']) ? $_GET['per_page'] : $limit;
            $this->pagination_page  = isset($_GET['page']) ? $_GET['page'] : 1;
        }
    }
	
    /**
     * Ajax error
     * 
     * @param array $errors Errors array
     * 
     * @return void
     */
    protected function _ajaxError($errors = array()) 
    {
        if (!is_array($errors)) {
            $errors = array($errors);
        }

        $this->disableView();
        header("HTTP/1.0 404 Not Found");
        $error = implode(', ', $errors);
        echo $error;
        die;
    }
    
    /**
     * Ajax modal error
     * 
     * @param array $errors Errors array
     * 
     * @return void
     */
    protected function _ajaxModalError($message = '', $new_url = 'ajax/modal/error') {
        
        if (!empty($this->user)) {
    	    $this->user->setRedirectUrl($_SERVER['REQUEST_URI']);
        }
    	$this->redirect302($new_url, $message, Vanilla_Message::ERROR);
    }
    
    /**
     * Check if smarty var has been set
     */
    protected function isSmartyAssigned($var) {
    	
    	if (empty($this->smarty->$var))
    	{
        	return false;
    	}
        return get_class($this->smarty->getVariable($var)) ==  'Smarty_Variable';
    }
    
    /**
     * Get order by id
     *
     * @param array $allowed_fields Array of array fields
     * 
     * @return void;
     */
    public function getOrderBy($allowed_fields = array(), $default = "date_created DESC")
    {
        $field = trim($this->request->getGet('field'));
        
        if(!in_array($field, $allowed_fields) || empty($field))
        {
            return $default;
        }
        else
        {
            $order = $field;
        }
        
        if(isset($_GET['order']) && $_GET['order'] == -1)
        {
            return $order." DESC";
        }
        return $order;
    }
}
