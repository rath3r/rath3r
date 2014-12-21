<?php
/**
 * 
 * Content MySQL Data Source
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage MySQL
 * @name Vanilla_MySQL_Connection
 * 
 */

class Vanilla_MySQL_Connection extends mysqli
{
	/**
	 * Singleton Instance
	 * @var Vanilla_MySQL_Connection
	 */	
	private static $instance;
	
	/**
	 * Constructor
	 * 
	 * @throws Vanilla_Exception_MySQL
	 */
	
	private function __construct()
	{
		@parent::__construct( 
	      		DB_HOST, 
	      		DB_USERNAME, 
	      		DB_PASSWORD, 
	      		DB_DATABASE
	      ); 
	      
	      if(mysqli_connect_errno()) 
	      { 
	         throw new Vanilla_Exception_MySQL( 
	            mysqli_connect_error(), 
	            mysqli_connect_errno() 
	         ); 
	      } 
	      
	      $this->set_charset(APP_ENCODING);
          $this->query("SET time_zone = 'UTC'");
		
    }
        
    /** 
     *  Initiating singleton of Database Connection
     *  
     *  @return Vanilla_MySQL_Connection Database instance
     */ 

    public function getInstance() 
    { 
    	if(self::$instance === null) 
		{ 
			$c = __CLASS__; 
			self::$instance = new $c; 
		} 
		return self::$instance; 
    } 
	
}