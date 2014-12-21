<?php

/**
 * Vanilla_Pagination
 *
 * @name     Vanilla_Pagination
 * @category Navigation
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Pagination
 *
 * @name     Vanilla_Pagination
 * @category Navigation
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Pagination extends Vanilla_Helper
{
    /**
     * Set page number
     * @var int
     */
    public $page_no;

    /**
     * Set limit of records per page
     * @var int
     */
    public $per_page;

    /**
     * Set total count of pages
     * @var int
     */
    public $total_count;

    /**
     * Set last page number
     * @var int
     */
    public $last_page;

    /**
     * Set previous page number
     * @var int
     */

    public $prev_page;

    public $params = array();
    
    /**
     * Set previous page number
     * @var int
     */

    public $per_page_values = array(10, 20, 50, 'ALL');

    /**
     * Set next page number
     * @var int
     */
    public $next_page;
    
    public $smarty;
    
    public $template = "long.tpl";

    /**
     * Construct pagination
     * 
     * @param Vanilla_Model_Rowset $rowset Fetched Rowset
     * @param string $url      Base Url
     * 
     * @return Vanilla_Pagination
     */
    public function __construct(Vanilla_Model_Rowset $rowset, $params = array(), $url = null)
    {
        $this->setPageNo($rowset->getQueryPage());
        $this->setPerPage($rowset->getQueryLimit());
        $this->shown_count = count($rowset->rowset);
        $this->setTotalCount($rowset->getCount());
        $this->setUrl($url = null);
        $this->setParams($params);
        $this->pagination();
    }
    
    /**
     * Set params
     * 
     * @param type $params
     * 
     * @return \Vanilla_Pagination
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }
    
    /**
     * Create the paginator based on passed parameters.
     * url will always be the base url for creating of the pagination
     * 
     * @return Vanilla_Pagination
     */

    public function pagination()
    {
        $this->checkIfParamsAreSet();
        $this->page_url      = $this->getUrl($this->page_no);
        $this->last_page     = ceil($this->total_count / $this->per_page);
        $this->last_page_url = $this->getUrl($this->last_page);

        $this->item_start    = ($this->per_page * ($this->page_no - 1)) + 1;

        if ($this->page_no < $this->last_page) {
            $this->next_page     = $this->page_no + 1;
            $this->next_page_url = $this->getUrl($this->next_page);
        }

        if ($this->page_no > 1) {
            $this->prev_page = $this->page_no - 1;
            $this->prev_page_url = $this->getUrl($this->prev_page);
        }

        $this->_setPerPageLinks();
        return $this;
    }

    /**
     * Checking if the crucial parameters for pagination have been set
     * If not, trigger error
     * 
     * @return void
     */
    public function checkIfParamsAreSet()
    {
        if ($this->page_no === null)
        {
            trigger_error("Make sure page_no options for your pagination are set");
        }
        if ($this->per_page === null)
        {
            trigger_error("Make sure per_page options for your pagination are set");
        }
        if($this->total_count === null)
        {
            trigger_error("Make sure total_count options for your pagination are set");
        }
        if ($this->url === null)
        {
            trigger_error("Make sure url options for your pagination are set");
        }
    }

    /**
     * Set the page number
     * 
     * @param int $page_no Page Number
     * 
     * @chainable
     * 
     * @return Vanilla_Pagination
     */
    public function setPageNo($page_no)
    {
        $this->page_no = $page_no;
        return $this;
    }

    /**
     * Set number of elements per page
     * 
     * @param int $per_page Number of records per page
     * 
     * @chainable
     * 
     * @return Vanilla_Pagination
     */
    public function setPerPage($per_page)
    {
        if($per_page == 'ALL')
        {
            $per_page = 1000;
        }
        $this->per_page = $per_page;
        return $this;
    }

    /**
     * Set Total Count for the items paginated
     * 
     * @param int $total_count Total Count of elements
     * 
     * @return void
     */
    public function setTotalCount($total_count)
    {
        $this->total_count = $total_count;
        return $this;
    }

    /**
     * Set the base url for the links
     * 
     * @param string $url Url
     * 
     * @chainable
     * 
     * @return Vanilla_Pagination
     */
    public function setUrl($url = null)
    {
        if($url === null)
        {
            $url = new Vanilla_Url;
        }
        $this->url = $url;
    }

    /**
     * Set Number of records per page
     * 
     * @param array $array Array
     * 
     * @return void
     */
    public function setPerPageValues($array)
    {
        $this->per_page_values = $array;
        $this->_setPerPage();
    }

    /**
     * Create the URL for the page number specified. Will use the url passed in constructor
     * and add all the parameters that are required
     * 
     * @param int $page_no  Page Number
     * @param int $per_page Number of records per page
     * 
     * @return string
     */

    public function getUrl($page_no = null, $per_page = null)
    {
        if (null === $page_no) {
            $page_no = $this->page_no;
        }
        if (null === $per_page) {
            $per_page = $this->per_page;
        }
        parse_str($this->url->getQuery(), $current_query);
        if (isset($current_query['ajax'])) {
            unset($current_query['ajax']);
        }
        $query_string  = array_merge($current_query, array('page' => $page_no, 'per_page' => $per_page), $this->params);
        return $this->url->getPath(). "?".http_build_query($query_string);
    }

    /**
     * Setting Per Page Values
     * 
     * @return void
     */
    private function _setPerPageLinks()
    {
        if (empty($this->per_page_links))
        {
            foreach ($this->per_page_values as $value) {
                if ($value == $this->per_page) {
                    $node = array('value' => $value);
                } else {
                    $node = array('value' => $value, 'url' => $this->getUrl(null, $value));
                }
                $this->per_page_links[] = $node;
            }
        }
    }
    
    public function __toString()
    {
        $this->initSmarty();
        $this->smarty->assign('pagination', $this);
        return $this->smarty->fetch("_pagination/".$this->template);
        
    }

}