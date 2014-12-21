<?php

/**
 * Debug Class
 *
 * @name     Vanilla_Debug
 * @category Debug
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */


/**
 * Debug Class
 * 
 * @name     Vanilla_Debug
 * @category Debug
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Debug
{

    public function __construct()
    {
        if($this->canDebug())
        {
            $this->initFirePHP();
        }
    }
    
    public function initFirePHP()
    {
        if(!class_exists("FirePHP"))
        {
            require_once "ext/FirePHPCore/fb.php";
        }
    }
    
    public function canDebug()
    {
        if(defined('DEBUG_ENABLED') && DEBUG_ENABLED == true)
        {
            return true;
        }
        return false;
    }
    
    /**
     * Display a message in FirePHP
     * 
     * @param string $message Error Message
     * @param string $type    Type of Error
     * 
     * @return void
     */

    public static function FirePHP($message, $type = 'LOG')
    {
        if(self::canDebug())
        {
            self::initFirePHP();
            self::_displayError($message, null, FirePHP::LOG);
        }
    }

    public static function startTimer()
    {
        if($_SESSION['debug_total_start_time'] === null)
        {
            $_SESSION['debug_total_start_time'] = time();
        }
        $_SESSION['debug_start_time'] = time();
    }
    
    public static function endTimer($message)
    {
        $start_time = $_SESSION['debug_total_start_time'];
        $diff       = time() - $start_time;
        self::FirePHP("END TIME: " . $message . " " . $diff . " seconds");
        $_SESSION['debug_total_start_time'] = null;
    }
    
    public static function showTime($message)
    {
        $start_time = $_SESSION['debug_start_time'];
        $diff       = time() - $start_time;
        self::FirePHP("Run time: " . $message . " " . $diff . " seconds");
        self::startTimer();
    }
    
    /**
     * Display a warning
     * 
     * @param string $message Error Message 
     * 
     * @return void
     */

    public static function Warn($message)
    {
        if(self::canDebug())
        {
            self::initFirePHP();
            self::_displayError($message, null, FirePHP::WARN);
        }
    }

    /**
     * Display a SQL in FirePHP
     * 
     * @param string $message Error Message
     * @param string $type    Error Log
     * 
     * @return void
     */

    public static function SQL($message, $type = 'log')
    {
        if(self::canDebug())
        {
            switch(strtolower($type))
            {
                case "error":
                    if(APPLICATION_ENVIRONMENT == "production")
                    {
                        self::_displayError($message, array("Error", "SQL"));
                    }
                    else 
                    {
                        self::initFirePHP();
                        self::_displayError($message, 'SQL', FirePHP::ERROR);
                    }
                    break;
                default:
                    if(defined('DEBUG_DB') && DEBUG_DB == true)
                    {
                        self::initFirePHP();
                        self::_displayError($message, 'SQL', FirePHP::LOG);
                    }
                    break;
            }
        }
    }

    /**
     * General send message function
     * 
     * @param string $message Error Message
     * @param string $prepend (prepending with string, for categorising)
     * @param string $type    Error Type
     * 
     * @return void
     */
    private static function _displayError($message, $prepend, $type = FirePHP::ERROR)
    {
        if(!headers_sent())
        {
            ob_start();
            self::initFirePHP();
            FB::send($message, $prepend, $type);
        }
        else
        {
            trigger_error($message);
        }
    }


    /**
     * Gets the memory peak value and returns it via FirePHP
     * 
     * @return null
     */
    public static function getMemoryPeak()
    {
        if(
            (defined('DEBUG_ENABLED') && DEBUG_ENABLED == true)
            || (defined('DEBUG_MEMORY') && DEBUG_MEMORY == true )
            )
        {
                self::initFirePHP();
                $size    = memory_get_peak_usage(true);
                $message = self::_translateMemorySize($size);
                self::_displayError($message, 'MEMORY PEAK', FirePHP::LOG);
        }
    }

    /**
     * Get memory usage and returns it via FirePHP
     * 
     * @param string $prepend String to pretend the debug output with 
     * 
     * @return void
     */

    public static function getMemoryUsage($prepend = null)
    {
        if(
            (defined('DEBUG_ENABLED') && DEBUG_ENABLED == true)
            || (defined('DEBUG_MEMORY') && DEBUG_MEMORY == true )
            )
       {
            self::initFirePHP();
            $size = memory_get_usage(true);
            $message = self::_translateMemorySize($size);
            self::_displayError($prepend." ". $message, 'MEMORY', FirePHP::LOG);
        }
    }
    /**
     * Translating Memory Size to be human readible instead of bytes
     * Returns size in bytes, kilobytes, megabates etc.
     * 
     * @param int $size Size in bytes
     * 
     * @return string
     */

    private static function _translateMemorySize($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];
    }

    /**
     * Trigger an error and return it via FirePHP
     * 
     * @param string $message Error Message
     * 
     * @return void
     */

    public static function Error($message)
    {
        if (self::canDebug())
        {
            if(APPLICATION_ENVIRONMENT == "production")
            {
                Vanilla_Debug_Amon::log($message, array("Error"));
            }
            else 
            {
                self::initFirePHP();
                FB::send($message, null, FirePHP::ERROR);
            }
            
        }
    }
    
    public static function errorTypeToName($type)
    {
        switch($type)
        {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_CORE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_CORE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return $type;
    }

}
