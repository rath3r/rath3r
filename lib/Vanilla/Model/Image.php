<?php

/**
 * @name Vanilla_Model_Image
 * @author Gerard L. Petersen (Freelancer) <gerard.petersen@circuitbakery.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Image extends Vanilla_Model_Row
{
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Image";
	
	/**
	 * Define required fields, that will be checked against during validation
	 * @var array
	 */
	public $required_fields = array ('title');
	
	/**
	 * Entity type for relationships 
	 * @var int
	 */
	public $entity_id = 6;

	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::validate()
	 * @param array $data
	 * @return array
	 */
	public function validate($data)
	{
		$ext = end(explode(".",$data['name']));
		if(isset($this->allowed_extensions) && isset($this->allowed_filetypes) &&
			(!in_array($ext, $this->allowed_extensions) || !in_array($data['filetype'], $this->allowed_filetypes)))
		{
			$this->errors[] = "You can't upload files of that type. Please upload: " . implode(" ,or ", $this->allowed_extensions). " file";
		}
		
		if(count($this->errors) > 0) 
		{
			return $this->errors;
		}
		return true;
	}
}