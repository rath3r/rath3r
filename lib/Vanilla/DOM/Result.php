<?php

/**
 * Vanilla_DOM_Result
 *
 * @name       Vanilla_DOM_Result
 * @category   DOM
 * @package    Vanilla
 * @subpackage DOM
 * @author     Dan Conaghan <dconaghan@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_DOM_Result
 *
 * @name       Vanilla_DOM_Result
 * @category   DOM
 * @package    Vanilla
 * @subpackage DOM
 * @author     Dan Conaghan <dconaghan@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */


class Vanilla_DOM_Result
{
    /**
     * Number of results
     * @var int
     */
    protected $_count;

    /**
     * CSS Selector query
     * @var string
     */
    protected $_css_query;

    /**
     * @var DOMDocument
     */
    protected $_document;

    /**
     * @var DOMNodeList
     */
    protected $_node_list;

    /**
     * Current iterator position
     * @var int
     */
    protected $_position = 0;

    /**
     * @var DOMXPath
     */
    protected $_xpath;

    /**
     * XPath query
     * @var string
     */
    protected $_xpath_query;

    /**
     * Constructor
     *
     * @param string       $css_query   CSS Query
     * @param string|array $xpath_query XPath Query
     * @param DOMDocument  $document    Document
     * @param DOMNodeList  $node_list   Node List
     * 
     * @return void
     */
    public function  __construct($css_query, $xpath_query, DOMDocument $document, DOMNodeList $node_list)
    {
        $this->_css_query   = $css_query;
        $this->_xpath_query = $xpath_query;
        $this->_document    = $document;
        $this->_node_list   = $node_list;
    }

    /**
     * Retrieve CSS Query
     *
     * @return string
     */
    public function getCssQuery()
    {
        return $this->_css_query;
    }

    /**
     * Retrieve XPath query
     *
     * @return string
     */
    public function getXpathQuery()
    {
        return $this->_xpath_query;
    }

    /**
     * Retrieve DOMDocument
     *
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->_document;
    }

    /**
     * Iterator: rewind to first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->_position = 0;
        return $this->_node_list->item(0);
    }

    /**
     * Iterator: is current position valid?
     *
     * @return bool
     */
    public function valid()
    {
        if (in_array($this->_position, range(0, $this->_node_list->length - 1)) && $this->_node_list->length > 0) {
            return true;
        }
        return false;
    }

    /**
     * Iterator: return current element
     *
     * @return DOMElement
     */
    public function current()
    {
        return $this->_node_list->item($this->_position);
    }

    /**
     * Iterator: return key of current element
     *
     * @return int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * Iterator: move to next element
     *
     * @return void
     */
    public function next()
    {
        ++$this->_position;
        return $this->_node_list->item($this->_position);
    }

    /**
     * Countable: get count
     *
     * @return int
     */
    public function count()
    {
        return $this->_node_list->length;
    }
}
