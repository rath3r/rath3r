<?php

/**
 * Vimeo API Connector
 * Use this function for easy connection to the API
 *
 * @name       Vanilla API Vimeo
 * @category   API
 * @package    Vanilla
 * @subpackage API
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Vimeo API Connector
 * Use this function for easy connection to the API
 *
 * @name       Vanilla API Vimeo
 * @category   API
 * @package    Vanilla
 * @subpackage API
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

class Vanilla_API_Vimeo
{

    /**
     * API Host
     * @var string
     */
    private $_host = "http://vimeo.com/api/";

    /**
     * Select version of api you want to use
     * @var string
     */
    private $_method  = "GET";


    /**
     * Variable to hold the URL
     * @var unknown_type
     */
    public $url;

    /**
     * API KEY for Vimeo
     * @var string
     */
    const API_KEY = "a7eef47333a6ce1c245fd798a1ad0ad2";
    
    /**
     * API SECRET for Vimeo
     * @var string
     */
    const API_SECRET = "d6d7121fe18a409e";

    /**
     * Set Url if different from original
     * 
     * @chainable
     * 
     * @return Vanilla_API_Vimeo
     */
    private function _setUrl()
    {
        $this->url = $this->_host;
    }

    /**
     * Set methods into the url
     * 
     * @param array $methods Methods
     * 
     * @chainable
     * 
     * @return Vanilla_API_Vimeo
     */
    public function setMethods($methods)
    {
        $this->_setUrl();
        $this->url .= implode("/", $methods);
        return $this;

    }

    /**
     * Set request method for vimeo API
     * 
     * @param string $request_method (POST|GET|PUT)
     * 
     * @chainable
     * 
     * @return Vanilla_API_Vimeo
     */
    public function setRequestMethod($request_method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Set request parameters
     * 
     * @param array $params (POST|GET|PUT)
     * 
     * @chainable
     * 
     * @return Vanilla_API_Vimeo
     */
    public function setParams($params)
    {
        $this->url .= "?" . http_build_query($params);
        return $this;
    }

    /**
     * Creating an API Request that will curl the server
     * 
     * @return Object
     */

    public function request()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        Vanilla_API::setMethodHeaders(&$ch);
        $response = curl_exec($ch);
        try
        {
            Vanilla_API::checkResponse($ch);
        }
        catch(Vanilla_Exception $e)
        {
            Vanilla_Debug::Error($e->getMessage());
        }
        curl_close($ch);
        return $response;

    }

}