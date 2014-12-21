<?php

/**
 * Request
 *
 * @name     Vanilla Request
 * @category Request
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Request
 *
 * @name     Vanilla Request
 * @category Request
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Request
{
    /**
     * POST variables
     * @var unknown_type
     */
    public $post;

    /**
     * GET variables
     * @var unknown_type
     */
    public $get;

    /**
     * Constructor
     *
     * @return Vanilla_Request
     */
    public function __construct()
    {
        $this->_setPOST();
        $this->_setGET();
        $this->_setPreviousUrl();
    }

    private function _setPreviousUrl()
    {
        if(!$this->isAjax() && $this->isTextHtml() && isset($_SERVER['HTTP_REFERER']))
        {
            $url = new Vanilla_Url();
            if (isset($_SESSION['previous_url']))
            {
                if($_SERVER['HTTP_REFERER'] != $url->toString(true)) 
                {
                    $_SESSION['previous_url'] =  $_SERVER['HTTP_REFERER'];
                }
            } 
            else 
            {
                $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
            }
        }
    }
    
    /**
     * Get $_GET variable and sanitize it
     * This function uses filter_input function to sanitize the input.
     * For the list of available filters, please view the link below
     * 
     * @param string $key    $_GET Key 
     * @param int    $filter Filter constant
     * 
     * @link http://www.php.net/manual/en/filter.filters.php
     * 
     * @return string
     */
    public function getGet($key, $filter = FILTER_SANITIZE_STRING)
    {
        if(!isset($_GET[$key]))
        {
            return null;
        }
        $variable = $_GET[$key];
        $variable = filter_var($variable, $filter);
        return $variable;
    }
    
	/**
     * Get $_POST variable and sanitize it
     * This function uses filter_input function to sanitize the post input.
     * For the list of available filters, please view the link below
     * 
     * @param string $key    $_POST Key 
     * @param int    $filter Filter constant
     * 
     * @link http://www.php.net/manual/en/filter.filters.php
     * 
     * @return string
     */
    public function getPost($key, $filter = FILTER_SANITIZE_STRING)
    {
        if(!isset($_POST[$key]))
        {
            return null;
        }
        $variable = $_POST[$key];
        return filter_var($variable, $filter);
    }
    
    /**
     * Generic get filtered input function
     * 
     * @param int    $filter Filter constant
     * @param int    $input  Input type constant
     * @param string $key    Input variable key
     * 
     * return string
     */
    public function getFilteredInput($filter = FILTER_SANITIZE_STRING, $input, $key)
    {
        return filter_input($input, $key, $filter);
    }
    
    public function getPreviousUrl()
    {
        return $_SESSION['previous_url'];
    }
    
    /**
     * Checking if the header is of text/html type
     * 
     * @return void
     */
    public function isTextHtml()
    {
        $headers = headers_list();
        foreach($headers as $header)
        {
            if(preg_match("/content-type\: text\/html/", $header))
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Set POST variables
     *
     * @return void
     */
    private function _setPOST()
    {
        foreach($_POST as $key => $value)
        {
            $this->post->$key = $value;
            //$this->post->$key = $this->getPost($key);
        }
    }

    /**
     * Set GET variables
     *
     * @return void
     */
    private function _setGET()
    {
        foreach($_GET as $key => $value)
        {
            $this->get->$key = $value;
            //$this->get->$key = $this->getGet($key);
        }
    }

    /**
     * Checking if the request is Post
     *
     * @return boolean
     */
    final public function isPost()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $this->sentFromSameServer())
        {
            return true;
        }
        return false;
    }
    
    /**
     * Checking if the request was sent from the same host
     * 
     * @return boolean
     */
    final public function sentFromSameServer()
    {
        $url = parse_url($_SERVER['HTTP_REFERER']);
        if($_SERVER['HTTP_HOST'] == $url['host'])
        {
            return true;
        }
        return false;
    }

    /**
     * Checks if the Request to the method is an Ajax request
     *
     * @return boolean
     */

    final public function isAjax()
    {
        if(
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        )
        {
            return true;
        }

        return false;
    }

    /**
     * Will return all the posted form values
     * skips any sort of submit buttons
     * 
     * @return array
     */
    public function getPostedFormValues()
    {
        foreach($this->post as $key => $value)
        {
            if (!preg_match("/^submit.*/", $key))
            {
                $_tmp[$key] = $value;
            }
        }
        return $_tmp;
    }

}
