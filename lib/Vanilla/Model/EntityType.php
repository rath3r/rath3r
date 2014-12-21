<?php

/**
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * @name Vanilla_Model_EntityType
 * 
 */

class Vanilla_Model_EntityType extends Vanilla_Model_Row
{
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "EntityType";
	
	public function getObject()
	{
	    if(Vanilla_Module::isInstalled($this->module) || empty($this->module))
        {
            $class_name = "Model_" .$this->model;
            if(class_exists($class_name))
            {
               $object = new $class_name();
               return $object;
            }
            else 
            {
                $tmp = explode("_", $class_name);
                $class_name = $tmp[0]. "_" . ucwords($this->module) . "_" . $tmp[1];   
                
                $object = new $class_name();
                return $object;
            }
        }
        return null;
	}
	
	
}