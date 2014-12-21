<?php

/**
 * Vanilla_Output_XML
 * Returns data as XML
 * 
 * @package    Vanilla
 * @subpackage Output
 * @author     Kasia Gogolek <kasia-gogolek@living-group.com>
 * 
 */

class Vanilla_Output_XML implements Vanilla_Interface_Output {
	
	/**
	 * Stores response values
	 * @var Object
	 */
	public $response;
	
	/**
	 * Stores data values
	 * @var Array
	 */
	public $data;
	
	
	public function __construct($data)
	{
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Interface_Output::__toString()
	 * @return string
	 */
	public function __toString()
	{
		
	}

} 