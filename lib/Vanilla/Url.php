<?php

/**
 * URL Class
 *
 * @name     Vanilla Url
 * @category Url
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * URL Class
 *
 * @name     Vanilla Url
 * @category Url
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Url
{
    /**
     * Url components
     * @var Array
     */
    public $url;

    /**
     * Path array of all strings that create it
     * @var Array
     */
    public $path;

    /**
     * Constructor grabs the current url, parses is, and assigns the path array
     */

    public function __construct()
    {
        $url = self::getProtocol() . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->url = parse_url($url);
        $this->_assignPathArray();

    }
    /**
     * Get Host name
     * 
     * @return string
     */
    public static function getHostName()
    {
        return self::getProtocol(). $_SERVER['SERVER_NAME'];
    }
    
    /**
     * Get protocol
     * 
     * @return string
     */
    public static function getProtocol()
    {
        if(self::isSSL())
        {
            return 'https://';
        } 
        return 'http://';
    }
    
    /**
     * Get Same path, just without language.
     * Language should always be the first param, so we're going to find it, 
     * and replace it
     * 
     * @example $url = "/fr/about-us/contact will return /about-us/contact
     * 
     * @return string
     */
    public static function getUrlWithoutLanguage()
    {
        $object = new self();
        $url    = $object->toString();
        $_tmp   = explode("/", $url);
        if(strlen($_tmp[1]) == 2 && $_tmp[1] != "js")
        {
            // we found language
            unset($_tmp[1]);
        }
        $new_url = implode("/", $_tmp);
        return $new_url;
    }
    
    /**
     * Assings Path Array
     * 
     * @example /view/profile/person-id will return array('view', 'profile', 'person-id')
     * 
     * @return void
     */

    private function _assignPathArray()
    {
        $this->path = explode("/", $this->url['path']);
        $this->path = array_filter($this->path);
    }

    /**
     * Returns Array of the element
     * 
     * @return Array
     */
    public function toArray()
    {
        $this->url['url_path'] = $this->path;
        return $this->url;
    }

    /**
     * Create URL from Route
     * Pass the route_id and parameters that are expected (if any)
     * this function will return a string for the url
     * 
     * @param string $route_id            Route ID
     * @param array  $params              Params
     * @param array  $query_string_params Query String Params
     * 
     * @return string
     */
    public static function createURLFromRoute($route_id, $params = NULL, $query_string_params = NULL)
    {
        $routes = Vanilla_Router::getAllRoutesAsArray();
        if (!isset($routes[$route_id]))
        {
            trigger_error("Can't find Route with the id:". $route_id);
        }
        $route  = $routes[$route_id];
        unset($routes);

        return self::createURLFromRouteArray($route, $params, $query_string_params);
    }
    
    /**
     * Create URL from route array
     * Once we found the route array, we can create the url
     * 
     * @param array  $route_array         Array of all route params
     * @param array  $params              Params array
     * @param string $query_string_params String of params 
     * 
     * @return string
     */
    public static function createURLFromRouteArray($route_array, $params = null, $query_string_params = null)
    {
        $url  = self::addLanguagePrefix();
        $url .= strip_regex_from_route($route_array['pattern']);
        
        // overwrite the params
        if ($params !== NULL) {
            foreach ($params as $key => $value) {
                $pattern = "/\(\?\<".$key."\>(.|\\\\w|\\\\d){1}\+\)/";
                $url = preg_replace($pattern, $value, $url);
            }
        }

        $url .= self::getQueryString($query_string_params);
        return $url;
    }
    
    /**
     * Adding Language Pefix if needed
     * 
     * @return void
     */
    public static function addLanguagePrefix()
    {
        if(
            Vanilla_Module::isInstalled("Languages") 
            && defined('LANGUAGES_PREFIX_URL')
            && LANGUAGES_PREFIX_URL == true)
        {
            $locale   = Vanilla_Locale::getFromSession();
            $language = $locale->getLanguage();
            if($language !== null && $language !== LANGUAGES_DEFAULT && !empty($language))
            {
                return "/" . $language;
            }
        }
        return null;
    }


    /**
     * Get Query String
     * 
     * @param array $query_string_params Query String Params
     * 
     * @return mixed
     */
    public static function getQueryString($query_string_params = NULL)
    {
        if ($query_string_params != NULL) {
            return "?".http_build_query($query_string_params);
        }
        return null;
    }

    /**
     * Magic method toString method;
     * Returns current url and query string
     * 
     * @return string
     */

    public function __toString()
    {

        return $this->toString();
    }

    /**
     * get Url String
     * Returns current url and query string
     * 
     * @return string
     */

    public function toString($hostname = false)
    {
        if($hostname === true)
        {
            $url = $this->getHostName();
        }
        else 
        {
            $url = "";
        }
        $url .= $this->url['path'];
        if (isset($this->url['query']))
        {
            return $url . "?". $this->url['query'];
        }
        return $url;
    }

    /**
     * Get Url path (without the hostname or query string)
     * 
     * @return string
     */

    public function getPath()
    {
        return $this->url['path'];
    }

    /**
     * Get Query string for url
     * 
     * @return string
     */
    public function getQuery()
    {
        if (isset($this->url['query'])) {
            return $this->url['query'];
        }
        return null;
    }
    
    /**
     * Get first URL segment
     * 
     * @return string
     */

    public function getFirstSegment()
    {
        $section    = end(array_reverse($this->path));
        return $section;
    }

    /**
     * Returns instance
     * 
     * @return Vanilla_Url
     */
    public static function factory()
    {
        return new self;
    }

    /**
     * Check if on SSL
     * 
     * @return boolean
     */
    public static function isSSL()
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
    }
    
    /**
     * Turn any string into url friendly one
     * 
     * @param string $string String to be parsed
     * 
     * @return string
     */
    public static function parseStringToUrl($string)
    {
    	$string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
        $string = str_replace(" ", "-", $string);
        $string = strtolower($string);
		$string = preg_replace("/-+/", '-', $string);
        return $string;
    }

}