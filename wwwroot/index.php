<?php
/**
 * @author Kasia Gogolek <kasia.gogolek@living-group.com
 * 
 */

/**
 * Using the default Vanilla_Bootstrap
 * If needs to be extended please, include it below
 */
set_include_path('../lib/Vanilla/');

defined('APPLICATION_ENVIRONMENT') ? true : define('APPLICATION_ENVIRONMENT', getenv('APPLICATION_ENVIRONMENT'));
defined('PATH_CONFIG') ? true : define('PATH_CONFIG', "../application/conf/");
defined('BASE_DIR') ? true : define('BASE_DIR', realpath(dirname(__FILE__)) ."/");

require_once('../lib/Vanilla/Bootstrap.php');
	
$bootstrap = new Vanilla_Bootstrap();
$bootstrap->run();
