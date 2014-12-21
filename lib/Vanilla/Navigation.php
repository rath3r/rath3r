<?php

/**
 * Vanilla Navigation
 * Builds navigation structure for all your needs
 *
 * @name     Vanilla Navigation
 * @category Navigation
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla Navigation
 * Builds navigation structure for all your needs
 *
 * @name     Vanilla Navigation
 * @category Navigation
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Navigation
{

    /**
     * Parent Id, numeric for pages from data source
     * string for routes
     * @var mixed
     */
    public $parent_id;

    /**
     * Parent Type Route or Page
     * @var string
     */
    public $parent_type;

    /**
     * All Navigation Nodes
     * @var array
     */
    public $nodes;

    /**
     * Sub Navigation
     */
    public $sub_navigation;

    /**
     * Select tracker
     * @var int
     */
    private $_select_tracker;

    /**
     * Constructor for Vanilla Navigation
     * Get All Pages from Data Source
     * Get All file routes
     * Create a navigation array
     * 
     * @param mixed            $parent_id Parent ID
     * @param string           $url_path  Current Url Path
     * @param Users_Model_User $user      User Object
     * 
     * @chainable
     * 
     * @return Vanilla_Navigation $this
     */
    public function __construct($parent_id = null, $url_path = null, $user = null, $parent_node = null)
    {
        if (is_null($parent_id)) {
            return $this;
        }

        $this->_getCurrentUser();
        $parent_id   = $this->_parseParentId($parent_id);
        $this->setUrlPath($url_path);
        $this->nodes = $this->_getNodeLayer($parent_id);
        
        if($parent_node !== null)
        {
            $this->parent_node = $parent_node;
        }
        
        $this->sub_navigation = $this->_getSubNavigation();
        
        return $this;
    }

    /**
     * Get Current user
     * 
     * @return void
     */
    protected function _getCurrentUser() 
    {
        $acl = new Vanilla_ACL();
        $this->user = $acl->getLoggedUser();
    }

    /**
     * Add an array of Vanilla_Navigation_Node objects to $this->nodes
     * 
     * @param array $nodes Nodes to be added
     * 
     * @chainable
     * 
     * @return Vanilla_Navigation
     */
    public function addNodes($nodes)
    {
        $this->nodes = $nodes;
        return $this;
    }

    /**
     * Getting nodes for one layer of parent_id
     * 
     * @param mixed $parent_id Parent ID
     * 
     * @return array
     */
    private function _getNodeLayer($parent_id)
    {
        $parent_id   = $this->_parseParentId($parent_id);
        $page_nodes  = $this->getPages($parent_id, $this->user);
        $route_nodes = $this->getFileRoutes($parent_id);
        $nodes       = $this->placeRouteNodes($page_nodes, $route_nodes);
        if (count($nodes)) {
            foreach ($nodes as &$node) {
                $node->children = $this->_getNodeLayer($node->id);
                if ($this->_select_tracker == $node->id) {
                    $this->_select_tracker = $node->parent_id;
                    $node->selected = true;
                }
            }
        }
        return $nodes;
    }

    /**
     * Parsing parent id to check if it's correct
     * If numeric return int, otherwise stick to string
     * 
     * @param mixed $parent_id Parent ID
     * 
     * @return mixed
     */
    private function _parseParentId($parent_id)
    {
        if (is_numeric($parent_id)) {
            $parent_id = (int) $parent_id;
        }
        return $parent_id;
    }

    /**
     * Whacks the file routes into the navigation in correct order.
     * 
     * @param array $page_nodes  Page Nodes
     * @param array $route_nodes Route Nodes
     * 
     * @return array
     */
    public function placeRouteNodes(array $page_nodes, array $route_nodes)
    {
        $unordered = array();
        foreach($route_nodes as $route)
        {
            if(isset($route->order) && $route->order != 0)
            {
                $not_bigger = 0;
                if (!empty($page_nodes))
                {
		    foreach ($page_nodes as $key => $page_node)
                    {
                        if ($page_node->order > $route->order)
                        {
			    $index      = array_search($key, array_keys($page_nodes));
			    $begining   = array_slice($page_nodes, 0, $index);
			    $reminder   = array_slice($page_nodes, $index, null, true);
                            $route_node = array($index => $route);
                            $page_nodes = array_merge($begining, $route_node, $reminder);
			    break;
                        }
                        else
                        {
                            $not_bigger++;
                        }
                    }
                }
                if($not_bigger == count($page_nodes))
                {
                   $unordered[] = $route;
                }
            } else {
                $unordered[] = $route;
            }
	    if(is_array($unordered))
            {
	        $page_nodes =  array_merge($page_nodes, $unordered);
	        $unordered  = array();
	    }
        }
        return $page_nodes;
    }


    /**
     * Get Pages from data source if the
     * parent id is numeric (parent comes from data source)
     * Otherwise do nothing and load the structure from routes
     * 
     * @param mixed $parent_id Parent ID
     * 
     * @return array $nodes
     */
    public function getPages($parent_id)
    {
        if(Vanilla_Module::isInstalled("Pages"))
        {
            $nodes = array();
            // If the parent is not a page, there's no point in getting any child pages
            if (is_numeric($parent_id)) {
                $pages = Model_Pages::factory()->getAll(
                    null, 
                    null, 
                    array('parent_id' => $parent_id, 'on_menu' => 'yes', 'status' => Vanilla_Model_Row::STATUS_LIVE), 
                    'order'
                );
                if (null === $pages->rowset) {
                    return array();
                }
                foreach ($pages->rowset as $page) {
                    if (
                        null === $this->user 
                        || ($this->user !== null 
                            && Vanilla_ACL::hasEntityPermission($this->user, $page)
                            ) 
                    ) {
                        $node = new Vanilla_Navigation_Node(
                            $page->id, 
                            $page->title, 
                            $this->url_path,  
                            $page->full_url, 
                            $page->parent_id, 
                            $page->order,
                            $page->subtitle
                        );
                        if ($node->selected) {
                            $this->_select_tracker = $node->parent_id;
                        }
                        $nodes[] = $node;
                    }
                }
                return $nodes;
            }
        }
        return array();
    }

    /**
     * Get all Routes loaded from routes directory
     * 
     * @param mixed $parent_id Parent ID
     * 
     * @return array $nodes
     */

    public function getFileRoutes($parent_id)
    {
        $routes_dir = getcwd() . "/". LIB_APP_DIR . Vanilla_Router::ROUTES_DIR;
        $handle = opendir($routes_dir);
        while (false !== ($file = readdir($handle))) {
            if (substr($file, 0, 1) != ".") {
                // removed INI_SCANNER_RAW to get it working on php < 5.3
                $data   = parse_ini_file($routes_dir. $file, true);
                foreach ($data as $key => $route) {
                    $menu_parent_id = isset($route['menu_parent']) ? $this->parseIntValue($route['menu_parent']) : null;
                    if ($menu_parent_id === $parent_id) {
                        $page     = Model_Page::factory()->createFromRoute($route, $key);
                        if (Vanilla_ACL::hasEntityPermission($this->user, $page, true)) {
                            $nodes[] = new Vanilla_Navigation_Node(
                                $key, 
                                $page->title,
                                $this->url_path,
                                $page->full_url,
                                $route['menu_parent'],
                                $page->order
                            );
                        }
                    }
                }
            }
        }
        if (isset($nodes)) {
            return $nodes;
        }
        return array();
    }

    /**
     * Set URL Path as class variable
     * 
     * @param string $url_path Url Path
     * 
     * @return void
     */
    public function setUrlPath($url_path)
    {
        $this->url_path = $url_path;
    }

    /**
     * Turn Object into a multi-dimensional array
     * 
     * @return $array
     */
    public function toArray()
    {
        $_tmp = array();
        if (count($this->nodes)) {
            foreach ($this->nodes as $node) {
                $_tmp[] = $node->toArray();
            }
        }
        return $_tmp;
    }

    /**
     * Parsing Int value
     * 
     * @param mixed $value Value to be cast as integer if numeric
     * 
     * @return mixed;
     */
    public function parseIntValue($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        return $value;
    }

    /**
     * Get sub navigation
     * 
     * @return mixed
     */
    protected function _getSubNavigation()
    {
        if(count($this->nodes) > 0)
        {
            foreach($this->nodes as $node)
            {
                if (isset($node->selected) && $node->selected)
                {
                    return new self($node->id, $this->url_path, null, $node);
                }
            }
        }

        return null;

    }

    /**
     * Factory
     * 
     * @return Vanilla_Navigation
     */
    public static function factory()
    {
        return new self();
    }

    /**
     * Deselect all selected pages in navigation
     *
     * @return void
     */
    public function deselectAll()
    {
        $this->_deselectNodes($this->nodes);
    }

    /**
     * Search and deselect selected node
     *
     * @param array $items Array of nodes
     *
     * @return void
     */
    protected function _deselectNodes($items)
    {
        foreach($items as $item)
        {
            if($item->selected)
            {
                $item->selected = false;
            }

            if(count($item->children) > 0)
            {
                $this->_deselectNodes($item->children);
            }
        }
    }

    /**
     * Set node as selected by URL
     *
     * @param string $url Url of the page
     *
     * @return void
     */
    public function selectNodeByUrl($url)
    {
        $this->_selectNodeByFullUrl($url, $this->nodes);
    }

    /**
     * Search and deselect selected node
     *
     * @param string $url Url of the page
     * @param array $items Array of nodes
     *
     * @return boolean
     */
    protected function _selectNodeByFullUrl($url, $items)
    {
        foreach($items as $item)
        {
            if($item->full_url == $url)
            {
                $item->selected = true;
                return true;
            }
            else
            {
                if(count($item->children) > 0)
                {
                    if($this->_selectNodeByFullUrl($url, $item->children))
                    {
                        $item->selected = true;
                    }
                }
            }
        }

        return false;
    }
}
