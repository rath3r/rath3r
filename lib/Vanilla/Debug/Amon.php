<?php

/**
 * Amon Client class for Vanilla
 * Allows us to connect to Amon.cx Server monitoring
 *
 * @name     Vanilla_Debug_Amon
 * @category Debug
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.3
 * @link     http://amon.cx/guide/clients/php/
 */

require_once BASE_DIR . "../lib/Vanilla/ext/Amon/amon.php";

/**
 * Debug Class
 * 
 * @name     Vanilla_Debug_Amon
 * @category Debug
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.3
 * @link     http://amon.cx/guide/clients/php/
 */

class Vanilla_Debug_Amon
{
    /**
     *  Initiating singleton of Database Connection
     *  
     *  @param string $message Message you want to log
     *  @param array  $tags    Tag this message
     *  
     *  @return void
     */

    public static function log($message, $tags = null)
    {
        return false;
	    $tags[] = APP_NAME;
        
        $tags[] = APPLICATION_ENVIRONMENT;

	    if(!empty($_ENV['USER']))
        {
            $tags[] = $_ENV['USER'];
        }
        $server = self::getServerUrl();
        
        Amon::config(array('address'=>  $server, 'protocol' => 'http'));
        Amon::setup_exception_handler();
        error_reporting(E_ALL);
        $math = 1 / 0;
        Amon::log($message, $tags);
    }
    
    /**
     * Get server url
     * if we're on rackspace 2 default to internal ip
     * just in case, otherwise the application will start hanging
     * 
     * @return string
     */
    public function getServerUrl()
    {
        $hostname = gethostname();
        if($hostname == "342775-web1.living-digital.com")
        {
            return "http://192.168.100.4:2464";
        }
        
        return AMON_SERVER;
    }

}
