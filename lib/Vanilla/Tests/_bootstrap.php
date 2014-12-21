<?php
define("APPLICATION_ENVIRONMENT", "production");
define("UNIT_TESTING", true);
define('BASE_DIR', realpath(dirname(__FILE__) . '/../../'));
defined('PATH_CONFIG') ? true : define('PATH_CONFIG', BASE_DIR."/../application/conf/");
$_SERVER['SERVER_NAME'] = "localhost"
?>
