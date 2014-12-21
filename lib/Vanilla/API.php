<?php

/**
 * API Connector
 * Use this function for easy connection to various APIs
 * 
 * @name     Vanilla_Api
 * @category Class
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * API Connector
 * Use this function for easy connection to various APIs
 * 
 * @name     Vanilla_Api
 * @category Class
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_API
{
    /**
     * API Key
     * @staticvar string
     */
    public static $key;

    /**
     * API Host
     * @staticvar string
     */
    public static $host;

    /**
     * Request Method (GET, POST, PUT etc)
     * @staticvar string
     */
    public static $method;

    /**
     * Post Params
     * @staticvar string
     */
    public static $post_params;

    /**
     * Query String
     * @staticvar string
     */
    public static $query_string;

    /**
     * API Url
     * @staticvar string
     */
    public static $url;


    /**
     * Creating an API Request that will curl the server
     * 
     * @param array  $path          array representing url methods
     * @param array  $params        query string params optional
     * @param string $method        method used to query api
     * @param string $return_format format in which we want to receive the response currently not supported on server
     * @param string $host          host we will be connecting to
     * @param string $key           API Key we want to use
     * 
     * @static
     * 
     * @return Object
     */

    public static function request($path = array(), 
        $params = array(), 
        $method = "GET", 
        $return_format = 'JSON', 
        $host = null, 
        $key = null)
    {
        self::$method = $method;
        self::setKey($key);
        self::setHost($host);
        self::setQueryString($params, $return_format);
        self::setUrl($path);
        try
        {
            return self::sendRequest($method);
        }
        catch (Vanilla_Exception $e)
        {
            Vanilla_Debug::Error($e->getMessage());
        }
    }

    /**
     * Sending the request to the API Server
     * 
     * @return string $response
     */

    public static function sendRequest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        self::setMethodHeaders($ch);
        $response = curl_exec($ch);
        try
        {
            self::checkResponse($ch);
        }
        catch(Vanilla_Exception $e)
        {
            Vanilla_Debug::Error($e->getMessage());
        }
        curl_close($ch);
        return $response;
    }

    /**
     * Set method specific curl options
     * 
     * @param curl $ch Curl Handler variable
     * 
     * @return curl handle
     */

    public function setMethodHeaders($ch)
    {
        switch(self::$method)
        {
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, self::$post_params);
                break;
            case "PUT":
                $request_length = filesize(self::$post_params['file']);
                $fh             = fopen('php://memory', 'rw');
                
                fwrite($fh, file_get_contents(self::$post_params['file']));
                rewind($fh);
                 
                curl_setopt($ch, CURLOPT_INFILE, $fh);
                curl_setopt($ch, CURLOPT_INFILESIZE, $request_length);
                curl_setopt($ch, CURLOPT_PUT, true);
                break;
        }
        return $ch;
    }

    /**
     * Checking response code for errors
     * 
     * @param cURL $ch Curl Handler variable
     * 
     * @return mixed
     */

    public static function checkResponse($ch)
    {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($http_code)
        {
            case "200":
                return true;
                break;
            case "404":
                throw new Vanilla_Exception("Wrong URL 404", "404");
                break;
            case "405":
                throw new Vanilla_Exception("Wrong URL 404", "404");
                break;
            case "0":
                throw new Vanilla_Exception("Request not found", "0");
                break;
        }
        throw new Vanilla_Exception("Unexpected Error", $http_code);
    }

    /**
     * Setting up the API Query string
     * 
     * @param string|null $params        Parameters for the query
     * @param string      $return_format Which format to return
     * 
     * @static
     * 
     * @return void
     */

    public static function setQueryString($params, $return_format)
    {
        $data['returnFormat'] = $return_format;
        $data['APIKey']       = self::$key;

        // get query, passing the params in querystring
        switch(self::$method)
        {
            case "GET":
                $data = array_merge($params, $data);
                break;
        }

        self::$post_params  = $params;
        self::$query_string = http_build_query($data);
    }

    /**
     * Setting up the API Key
     * If null, it returns API_KEY specified in config
     * 
     * @param string|null $key API Key
     * 
     * @static
     * 
     * @return string self::$key;
     */

    public static function setKey($key = null)
    {
        self::$key = API_KEY;
        if(null !== $key) 
        {
            self::$key = $key;
        }
        return self::$key;
    }

    /**
     * Setting up the API host
     * If null, it returns API_HOST specified in config
     * 
     * @param string|null $host Host name
     * 
     * @static
     * 
     * @return string self::$host;
     */

    public static function setHost($host = null)
    {
        if(self::$host == null)
        {
            self::$host = API_HOST;
        }
        if(null !== $host)
        {
            self::$host = $host;
        }
        return self::$host;
    }

    /**
     * Setting up the Full URL
     * 
     * @param array $method_array method array
     * 
     * @static
     * 
     * @return string self::$url;
     */

    public static function setUrl($method_array)
    {
        //adding returFormat to query string

        self::$url        = self::$host . "/";
        self::$url       .= implode("/", $method_array);
        self::$url       .= "?".self::$query_string;
        return self::$url;

    }
}