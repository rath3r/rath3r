<?php 

/**
 * 
 * Enter description here ...
 * @author Kasia Gogolek <kgogolek@living-group.com>
 * @package Vanilla
 * @subpackage Parser
 * @name Vanilla_Parser_ExcelFilter
 *
 */

class Vanilla_Parser_ExcelFilter implements PHPExcel_Reader_IReadFilter 
{ 
	/**
	 * Start Row
	 * @var int
	 */
    private $_startRow = 0; 
    
    /**
	 * End Row
	 * @var int
	 */
    private $_endRow   = 0; 
    
    /**
	 * Columns array
	 * @var array
	 */
    private $_columns  = array(); 

    /**
     * 
     * Get the list of rows and columns to read
     * @param int $startRow
     * @param int $endRow
     * @param array $columns
     */
    public function __construct($startRow, $endRow, $columns) { 
        $this->_startRow = $startRow; 
        $this->_endRow   = $endRow; 
        $this->_columns  = $columns; 
    } 
	
    /**
     * (non-PHPdoc)
     * @see PHPExcel_Reader_IReadFilter::readCell()
     * @param int $column
     * @param int $row
     * @param string $worksheetName
     */
    public function readCell($column, $row, $worksheetName = '') { 
        //  Only read the rows and columns that were configured 
        if ($row >= $this->_startRow && $row <= $this->_endRow) { 
            if (in_array($column,$this->_columns)) { 
                return true; 
            } 
        } 
        return false; 
    } 
} 

/**  Create an Instance of our Read Filter, passing in the cell range   
$filterSubset = new MyReadFilter(9,15,range('G','K')); **/
