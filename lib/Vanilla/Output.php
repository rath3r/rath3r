<?php
/**
 * Vanilla_Output
 * Initiates XML / JSON Output based on APP_OUTPUT_FORMAT constant
 * defined in the configuration file
 *
 * @name     Vanilla Output
 * @category Output
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Output
 * Initiates XML / JSON Output based on APP_OUTPUT_FORMAT constant
 * defined in the configuration file
 *
 * @name     Vanilla Output
 * @category Output
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Output
{

    /**
     * Initiate correct class JSON or XML
     *
     * @static
     *
     * @return Vanilla_Output_JSON|Vanilla_Output_XML|null $object
     */
    public static function init()
    {
        try
        {
            $format = self::setResponseFormat();
            switch(strtoupper(APP_OUTPUT_FORMAT))
            {
                case "JSON":
                    $object = new Vanilla_Output_JSON();
                    return $object;
                    break;
                case "XML":
                    $object = new Vanilla_Output_XML();
                    return $object;
                    break;
            }
            return null;
        }
        catch (Vanilla_Exception $e)
        {
            Vanilla_Debug::Warn($e->getMessage());
        }
    }

    /**
     * Sets response format. Function checks if a request has a set $_GET variable
     * That will define the response format, if not, use global setting for the API
     * If none of these can be found throw an exception
     *
     * @static
     * @throws Vanilla_Exception
     *
     * @return string
     */
    public static function setResponseFormat()
    {
        if (isset($_GET['returnFormat'])) {
            return $_GET['returnFormat'];
        }
        
        if (defined('APP_OUTPUT_FORMAT')) {
            return APP_OUTPUT_FORMAT;
        }
        throw new Vanilla_Exception(
            "Can't find format set up. Please make sure your config file contains constant APP_OUTPUT_FORMAT", 
            '404'
        );

    }

}