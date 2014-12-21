<?php
/**
 * @name Vanilla_Model_Articles
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */
class Vanilla_Model_Blogs extends Vanilla_Model_Rowset
{
	/**
	 * Name of the corresponding Row Class
	 * @var string
	 */
	public $row_class_name = "Vanilla_Model_Article";
	
	/**
	 * Get press releases
	 */
	protected function _getPressReleases() {
		
		$db_data = $this->getDAOInterface();
		$db_data->_getPressReleases();		
		
	}
	
	/**
	 * Find query for search, where query is the searched for keyword
	 * @param string $query
	 * @return Pages_Model_Pages
	 */
	public function findQuery($query, $page_no, $per_page, $count = false)
	{
		$db_page = $this->getDAOInterface();
		$db_page->findQuery($query, $page_no, $per_page, $count);
		if($count)
		{
			$total = end($db_page->getData());
			return $total['total'];
		}
		$this->assign($db_page);
		return $this;
	}

	
}