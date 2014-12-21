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

class Vanilla_MongoDB_Connection
{
	/**
	 * Singleton Instance
	 * @var Vanilla_MySQL_Connection
	 */	
	private static $instance;
	
	private $_connection;
	
	/**
	 * Constructor
	 * 
	 * @throws MongoConnectionException
	 */
	
	public function __construct()
	{
	    
    	try 
        {
            $this->_connection = new Mongo(DB_HOST);
	        $this->_db = $this->_connection->selectDB(DB_DATABASE);
        }
        catch ( MongoConnectionException $e ) 
        {
            echo '<p>Couldn\'t connect to mongodb, is the "mongo" process running?</p>';
            exit();
        }
    }
        
	 /** 
	  *  Initiating singleton of Database Connection
	  *  
	  *  @return Vanilla_MongoDB_Connection 
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
    
    /**
     * Extending the default functionality
     * Any call to this class will run on the MongoDB class
     * that was already initiated
     * 
     * @param string $name
     * @param array  $arguments
     * 
     * @magic
     * 
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->_db->$name($arguments);
    }
    
    public function __get($name)
    {
        return $this->_db->$name;
    }
    
    public function command($params)
    {
        return $this->_db->command($params);
    }
	
}