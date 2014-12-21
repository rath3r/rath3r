<?php

/**
 * 
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package Vanilla
 * @subpackage Tests
 * 
 */

require_once '../Bootstrap.php';

class Vanilla_Tests_Bootstrap extends PHPUnit_Framework_TestCase
{

	protected $_bootstrap = null;
	
	
	/**
	 * Check if can initialize the class
	 */
	public function setUp()
	{
		$this->_bootstrap = new Vanilla_Bootstrap();
	}
	
	/**
	 * Checking if production and staging ini exists
	 */
	public function testIniExists()
	{
		$this->assertFileExists(PATH_CONFIG . "production.ini", "Can't find production.ini file in your config files");
		$this->assertFileExists(PATH_CONFIG . "staging.ini", "Can't find staging.ini file in your config files");
	}
	
	/**
     * Loading Ini files
     * @depends testIniExists
     */
	public function testLoadIni()
	{
		$this->_bootstrap->initConfig();
		$this->assertInstanceOf('Vanilla_Config_Ini', $this->_bootstrap->config, "Config not loaded");
	} 
	
	/**
	 * Check if Encoding is set
	 * @depends testLoadIni
	 */    
    public function testEncoding()
    {
   		$this->assertTrue(defined('APP_ENCODING'), "APP_ENCODING is not set in application");
    }
    
    /**
     * Checking if salt is random and not empty
     * @depends testLoadIni
     */
    public function testSalt()
    {
    	$this->assertTrue(defined('APP_SALT'), "APP_SALT not set at all. Please edit your config");
    	
    	// checking if not default
    	$this->assertThat(APP_SALT, $this->logicalNot($this->equalTo("Hello World")), "APP_SALT has not been changed from default Hello World, please change it.");
    	
    	// checking if not empty
    	$this->assertThat(APP_SALT, $this->logicalNot($this->equalTo("")), "APP_SALT empty, please input random value");
    	
    	// checking if not equal to APP_NAME (too easy to guess)
    	$this->assertThat(APP_SALT, $this->logicalNot($this->equalTo(APP_NAME)), "APP_SALT the same as APP_NAME, please change");
    }
    
     /**
     * Testing Router files
     * @depends testLoadIni
     */
    public function testRouter()
    {
    	$this->assertObjectHasAttribute('config', $this->_bootstrap);
    	$this->assertTrue(is_array(ROUTER_FILES), "duda");
    }
    
    /**
	 * Check if Encoding is set
	 * @depends testLoadIni
	 */  
    public function testSmartyDirectories()
    {
    	$this->assertFileExists(BASE_DIR . "/" . SMARTY_COMPILE_DIR, "Smarty cache directory does not exsits");
    	$this->assertTrue(is_writable(BASE_DIR ."/" . SMARTY_COMPILE_DIR), "Smarty cache is not writable");
    }
       
    /**
     * Checking if any issues when unsetting bootstrap
     */
    
	public function tearDown()
	{
		unset($this->_bootstrap);	
	}
	
	
}