<?php



/**
 * 
 * Vanilla_Parser_CSV
 * @name Vanilla_Parser_CSV
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Parser
 * @version 1.2
 * 
 */

class Vanilla_Parser_CSV 
{
	/**
	 * get headers from object
	 * @param Object $object
	 * @return string
	 */
	public static function getHeadersFromArray($array)
	{
		$csvOutput = "";
		foreach($array as $label)
		{
			$cells[] = (string) self::parseFieldToLabel($label);
		}
		$csvOutput = implode(",", $cells)."\r\n";
		return $csvOutput;
	}		
	
	/**
	 * Get Data from collection
	 * @param Object $collection
	 * @return string
	 */
	public static function getDataFromCollection($table_columns, $collection)
	{
		$csvOutput = "";
		foreach($collection as $object)
		{
			$cells = array();
			foreach($table_columns as $key)
			{
			    if(isset($object->$key))
			    {
				    $cells[] = $object->$key;
			    }
			}
			$csvOutput .= implode(",", $cells)."\r\n";
		}
		
		return $csvOutput;
	}
	/**
	 * Get Data from collection
	 * @param Object $collection
	 * @return string
	 */
	public static function getDataFromArray($table_columns, $array)
	{
		$csvOutput = "";
		foreach($array as $object)
		{
			$cells = array();
			foreach($table_columns as $key)
			{
				$cells[] = '"' . $object[$key] . '"';
			}
			$csvOutput .= implode(",", $cells)."\r\n";
		}
		
		return $csvOutput;
	}

	
	/**
	 * Making the data labels from db more human readible
	 * @param string $label
	 * @return string
	 */
	public static function parseFieldToLabel($label)
	{
		$label = str_replace(array("_",","), " ", $label);
		$label = ucwords($label);
		return $label;
	}
	
}
