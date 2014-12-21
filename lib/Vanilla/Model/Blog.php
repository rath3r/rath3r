<?php
/**
 * @name Vanilla_Model_Article
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */
class Vanilla_Model_Blog extends Vanilla_Model_Row
{
	
	const ENTITY_ID = 11;
	
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Article";
	
	/**
	 * Entity ID for setting up relations
	 * @var int
	 */
	public $entity_id = self::ENTITY_ID;
	
	/**
	 * List of required fields that will be checked during validation
	 * @var array
	 */
	public $required_fields = array ('title', 'body', 'url');
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::update()
	 * @param $data array
	 * @return Vanilla_Model_Article
	 */
	
	public function update($data)
	{
		if(isset($data['image']) && empty($data['image']))
		{
			unset($data['image']);
		}
		
		$this->_sortOutDates($data);
		
		return parent::update($data);
	}

	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::create()
	 * @param $data array
	 * @return Vanilla_Model_Article
	 */
	public function create($data)
	{
		if(isset($data['image']) && empty($data['image']))
		{
			unset($data['image']);
		}
		
		$this->_sortOutDates($data);
		
		return parent::create($data);
	}
	
	protected function _sortOutDates( &$data ) {
		
		/*
		 * Build up datetime field
		 */
		$data['article_date'] = substr($data['article_date'],0,10);
		$data['article_time_hour'] = substr($data['article_time_hour'],0,2);
		$data['article_time_min'] = substr($data['article_time_min'],0,2);
		$data['article_date'] .= ' ' . $data['article_time_hour'] . ':' . $data['article_time_min'] . ':00';
		
		unset($data['article_time_hour']);
		unset($data['article_time_min']);
		
	}
	
}