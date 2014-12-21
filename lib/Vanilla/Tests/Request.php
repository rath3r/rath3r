<?php

/**
 * 
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package Vanilla
 * @subpackage Tests
 * 
 */

require_once BASE_DIR . '/Vanilla/Request.php';

class Vanilla_Tests_Request extends PHPUnit_Framework_TestCase
{

    protected $_request;
    
	/**
	 * Check if can initialize the class
	 */
	public function setUp()
	{
		$this->_request = new Vanilla_Request();
	}
	
	/**
	 * Checking if the sanitizer will sanitize a url
	 * is being returned as the same variable
	 */
	public function testGetSanitizeForUnsanitized()
	{
	    $unsanitized  = "<a href='fooBar.html'>foo bar</a>";
	    $_GET['test'] = $unsanitized;
	    $sanitized    = $this->_request->getGet('test');
	    $this->assertFalse($sanitized === $unsanitized);
	}
	
	/**
	 * Checking if a simple, sanitized variable
	 * is being returned as the same variable
	 */
    public function testGetSanitizeForSanitized()
	{
	    $unsanitized  = "foo";
	    $_GET['test'] = $unsanitized;
	    $sanitized    = $this->_request->getGet('test');
	    $this->assertTrue($sanitized === $unsanitized);
	}
	
	/**
	 * Checking if the sanitizer will sanitize a url
	 * is being returned as the same variable
	 */
	public function testPostSanitizeForUnsanitized()
	{
	    $unsanitized  = "<a href='fooBar.html'>foo bar</a>";
	    $_POST['test'] = $unsanitized;
	    $sanitized    = $this->_request->getPost('test');
	    $this->assertFalse($sanitized === $unsanitized);
	}
	
	/**
	 * Checking if a simple, sanitized variable
	 * is being returned as the same variable
	 */
    public function testPostSanitizeForSanitized()
	{
	    $unsanitized  = "foo";
	    $_POST['test'] = $unsanitized;
	    $sanitized    = $this->_request->getPost('test');
	    $this->assertTrue($sanitized === $unsanitized, "[x] bam");
	}
	
    /**
     * Checking if any issues when unsetting bootstrap
     */
    
	public function tearDown()
	{
		unset($this->_bootstrap);	
	}
	
	
}