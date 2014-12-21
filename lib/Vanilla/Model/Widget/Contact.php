<?php
class Model_Widget_Contact extends Vanilla_Model_Row
{

	
	const ENTITY_ID = 10;
		
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Widget_Contact";
	
	/**
	 * Entity ID for setting up relations
	 * @var int
	 */
	public $entity_id = self::ENTITY_ID;
	
}