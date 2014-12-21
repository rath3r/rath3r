<?php


//include(LIB_FRAMEWORK_DIR . "Vanilla/ext/PHPExcel.php");

/**
 * 
 * Vanilla_Parser_Excel
 * @name Vanilla_Parser_Excel
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Parser
 * 
 */

class Vanilla_Parser_Excel extends Vanilla_Parser
{
	/**
	 * Object Reader
	 * @var Object
	 */
	public $objReader;
	
	/**
	 * parse the file
	 * if $worksheets is specified, it will find the names of the worksheets and assign
	 * only the ones specified. Please note that the first spreadsheet will always have key = 0
	 * if you need sheets 1 and 4 $worksheets = array(1,4);
	 * @param unknown_type $filepath
	 * @param mixed $worksheets 
	 * @param Object $filterSubset
	 * @todo check if file is excel
	 * @return Object
	 */
	
	
	public function parse($filepath, $worksheets = null, $filterSubset)
	{
		
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp; 
		$cacheSettings = array('memoryCacheSize' => '32MB'); 
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 
		
		$this->objReader = PHPExcel_IOFactory::createReaderForFile($filepath);
		$this->objReader->setReadDataOnly(true);
		if(null !== $worksheets)
		{
			$sheets = $this->getWorksheetNames($filepath, $worksheets);
			$this->objReader->setLoadSheetsOnly($sheets);
			echo memory_get_usage()."<br/>";
		}	
		
		if(null !== $filterSubset)
		{
			$this->objReader->setReadFilter($filterSubset);
		}
		
		$objPHPExcel = $this->objReader->load($filepath);
		unset($this->objReader);
		return $objPHPExcel;
	}
	
	/**
	 * set Cache for phpExcel
	 * @param string $cache_size
	 */
	
	public function _setCache($cache_size)
	{
		// setting cache
		$cache_method = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip; 
		$cache_settings = array('memoryCacheSize' => $cache_size); 
		PHPExcel_Settings::setCacheStorageMethod($cache_method, $cache_settings); 
	}
	
	/**
	 * 
	 * Get names of worksheets by references
	 * note: first worksheet has reference 0
	 * @param string $filename
	 * @param array $ref
	 * @return array
	 */
	
	public function getWorksheetNames($filename, $ref = 0)
	{
		$list = $this->reader->listWorksheetNames($filename);
		foreach($list as $key => $value)
		{
			if(is_array($ref) && in_array($key, $ref))
			{
					$data[] = $value;
			}
			elseif(!is_array($ref) && $key == $ref)
			{
					$data = $value;
			}
		}
		return $data;
	}

		/**
		 * 
		 * Checking if the file is correct if no, return an error
		 * 
		 */
	
		private function _checkIfCorrectFile()
		{
			if($file['size'] == 0)
			{
				$errors[] = "File empty. Please upload again";
			}
			$ext = end(explode(".",$data['name']));
		}

		/**
		 * 
		 * Get Column (letter) from the cell name
		 * @param string $cell_id
		 * @return string
		 */
		
		protected function _getColumnName($cell_id)
		{
			preg_match("/(?P<letter>\D+)/", $cell_id, $matches);
			return $matches['letter'];
		}
		
		/**
		 * 
		 * Get Row number from the cell name
		 * @param string $cell_id
		 * @return int
		 */
		
		protected function _getRowName($cell_id)
		{
			preg_match("/(?P<number>\d+)/", $cell_id, $matches);
			return $matches['number'];
		}
		
		/**
		 * Get previous letter to the one, we're currently on
		 * @param string $cell_id
		 * @return string
		 */
		
		protected function _getHorizontalPreviousCell($cell_id)
		{
			$letter = $this->_getColumnName($cell_id);
			$letter = ord($letter);
			$letter = chr($letter - 1);
			return $letter;
		}
		
		/**
		 * 
		 * Get next value
		 * @param string $cell_id
		 * @return string
		 */
		
		protected function _getHorizontalNextCell($cell_id)
		{
			$letter = $this->_getColumnName($cell_id);
			$letter = ord($letter);
			$letter = chr($letter + 1);
			return $letter;
		}
		
		/**
		 * Get previous vertical cell to the one, we're currently on
		 * @param string $cell_id
		 * @return int
		 */
		
		protected function _getVerticalPreviousCell($cell_id)
		{
			$number = $this->_getRowName($cell_id);
			$number = $number - 1;
			return $number;
		}
		
		/**
		 * 
		 * Get vertical call next value
		 * @param string $cell_id
		 * @return int
		 */
		
		protected function _getVerticalNextCell($cell_id)
		{
			$number = $this->_getRowName($cell_id);
			$number = $number + 1;
			return $number;
		}
		

}
