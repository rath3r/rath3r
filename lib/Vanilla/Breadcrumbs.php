<?php
/**
 * Vanilla Breadcrumbs
 * Builds an array for the breadcrumbs
 * 
 * @name     Vanilla_Breadcrumbs
 * @category Navigation
 * @package  Vanilla
 * @author   Niall St John <niall.stjohn@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla Breadcrumbs
 * Builds an array for the breadcrumbs
 * 
 * @name     Vanilla_Breadcrumbs
 * @category Navigation
 * @package  Vanilla
 * @author   Niall St John <niall.stjohn@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Breadcrumbs
{

    /**
     * Breadcrumbs
     * @var array
     */
    public $breadcrumbs;

    /**
     * Constructor for Vanilla Breadcrumbs
     * Get an array to use as breadcrumbs
     * Get All file routes
     * Create a navigation array
     * 
     * @param Vanilla_Navigation $navigation Navigation Object
     * 
     * @chainable
     * 
     * @return Vanilla_Navigation $this
     */
    public function __construct($navigation)
    {
        $this->breadcrumbs = array();

        if (count($navigation->nodes)) {
            foreach ($navigation->nodes as $node) {
                if ($node->selected) {
                    $this->_addNode($node);
                }
            }
        }
    }

    /**
     * Add node to the breadcrumbs
     * 
     * @param Vanilla_Navigation_Node $node Node we want to add
     * 
     * @return void
     * 
     */
    protected function _addNode($node)
    {

        $this->breadcrumbs[] = array(
            'title' => $node->title,
            'full_url' => $node->full_url,
        );

        foreach ($node->children as $node) {
            if ($node->selected) {
                $this->_addNode($node);
                break;
            }
        }
    }

    /**
     * Add Crumb for the page
     *
     * @param string $title    Page Title
     * @param string $full_url Full Url for the page
     *
     * @return void
     */
    public function addCrumb($title, $full_url = null)
    {
        $this->breadcrumbs[] = array('title' => $title, 'full_url' => $full_url);
    }

    /**
     * Prepend crumb
     *
     * @param string $title    Page Title
     * @param string $full_url Full Url for the page
     *
     * @return void
     */
    public function prependCrumb($title, $full_url = null)
    {
        array_unshift($this->breadcrumbs, array('title' => $title, 'full_url' => $full_url));
    }

    /**
     * Remove last crumb
     *
     * @return void
     */
    public function removeLastCrumb()
    {
        array_pop($this->breadcrumbs);
    }

    /**
     * Remove first crumb
     *
     * @return void
     */
    public function removeFirstCrumb()
    {
        array_shift($this->breadcrumbs);
    }

    /**
     * Clear all crumbs
     *
     * @return void
     */
    public function clearAllCrumbs()
    {
        $this->breadcrumbs = array();
    }
}
