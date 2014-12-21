<?php

/**
 * Vanilla_Log
 *
 * @name     Vanilla_Log
 * @category Log
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.1
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Log
 *
 * @name     Vanilla_Log
 * @category Log
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.1
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Log
{
    
    /**
     * Log the message into a file
     * 
     * @return void
     */
    public static function log($message, $log_file = "general.log")
    {
        
        $log_file = BASE_DIR."../logs/".$log_file;
        $handle   = fopen($log_file, 'a+') or die('Cannot open file:  '.$log_file);
        $data     = $message."\n";
        fwrite($handle, $data);
    }
    
    
    
    
    
}