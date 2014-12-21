<?php

/**
 * Functions
 *
 * @name     Functions
 * @category Misc
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */


/**
 * prin_r variable
 * 
 * @param mixed $arg Variable we will print_r
 * 
 * @package Functions
 * 
 * @return void
 */

function pre($arg)
{
    echo "<pre>";
    print_r($arg);
    die;
}

/**
 * Turning ini setting name into a constant name
 * i.e. lib-dir to LIB_DIR
 * 
 * @param string $name Name of Constant
 * 
 * @package Functions
 * 
 * @return string
 */

function parse_constant_name($name)
{
    return strtoupper(str_ireplace(array(" ", "-"), "_", $name));
}



/**
 * Turning class names into directory structures for autoload
 * 
 * @param string $class_name Class Name
 * 
 * @example Vanilla_Controller_Home is Vanilla/Controller/Home
 * @package functions
 * 
 * @return string
 */

function parse_class_name_into_dir($class_name)
{
    return str_replace("_", DIRECTORY_SEPARATOR, $class_name);
}

/**
 * Turning route name into a normalised name
 * 
 * @param string $route_name Route Name
 * 
 * @example site-map to Site Map
 * @package functions
 * 
 * @return string
 */

function normalise_route_name($route_name)
{
    return ucwords(str_replace("-", " ", $route_name));
}

/**
 * Parsing the regex pattern to create a url
 * 
 * @param string $pattern Route Regex Pattern
 * 
 * @return string
 */

function strip_regex_from_route($pattern)
{
    //removing $ fromt the end
    $pattern   = trim($pattern);
    $strlen    = strlen($pattern);
    $first_char = substr($pattern, 0, 1);
    $last_char  = substr($pattern, $strlen -1, 1);
    if($last_char == "$")
    {
        $pattern = substr($pattern, 0, $strlen - 1);
    }
    if($first_char == "^")
    {
        $pattern = substr($pattern, 1, $strlen);
    }
    
    return $pattern;
}

/**
 * Turning variable names to proper words
 * i.e. first_name => First Name
 * 
 * @param string $name Name of column
 * 
 * @package functions
 * 
 * @return string
 */

function parse_name_to_word($name)
{
    return ucwords(str_replace("_", " ", $name));
}

if (!function_exists('get_called_class')) {
    
    /**
     * Retro-support of get_called_class()
     * Tested and works in PHP 5.2.4
     * http://www.sol1.com.au/
     * 
     * @param boolean $bt Debug Backtrace?
     * @param int     $l  Something else?
     * 
     * @package Functions
     * 
     * @return string
     */

    function get_called_class($bt = false,$l = 1) 
    {
        if (!$bt) $bt = debug_backtrace();
        if (!isset($bt[$l])) {
           throw new Exception("Cannot find called class -> stack level too deep."); 
        }
        if (!isset($bt[$l]['type'])) {
            throw new Exception('type not set');
        }
        else switch ($bt[$l]['type']) {
            case '::':
                $lines = file($bt[$l]['file']);
                $i = 0;
                $caller_line = '';
                do {
                    $i++;
                    $caller_line = $lines[$bt[$l]['line']-$i] . $caller_line;
                } while (stripos($caller_line, $bt[$l]['function']) === false);
                preg_match(
                    '/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                    $caller_line,
                    $matches
                );
                if (!isset($matches[1])) {
                    // must be an edge case.
                    throw new Exception("Could not find caller class: originating method call is obscured.");
                }
                switch ($matches[1]) {
                    case 'self':
                    case 'parent':
                        return get_called_class($bt, $l+1);
                    default:
                        return $matches[1];
                }
                // won't get here.
            case '->': switch ($bt[$l]['function']) {
                case '__get':
                    // edge case -> get class of calling object
                    if (!is_object($bt[$l]['object'])) {
                       throw new Exception("Edge case fail. __get called on non object."); 
                    }
                    return get_class($bt[$l]['object']);
                default: return $bt[$l]['class'];
            }

            default: throw new Exception("Unknown backtrace method type");
        }
    }
}