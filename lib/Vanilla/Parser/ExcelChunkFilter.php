<?php 

/**
 * @name chunkReadFilter
 * @package Vanilla
 * @subpackage Parser
 * @todo not sure if it's even used at the moment
 */

class chunkReadFilter implements PHPExcel_Reader_IReadFilter 
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
     * 
     * Set the list of rows that we want to read
     * @param unknown_type $startRow
     * @param unknown_type $chunkSize
     */
    public function setRows($startRow, $chunkSize) { 
        $this->_startRow = $startRow; 
        $this->_endRow   = $startRow + $chunkSize; 
    } 

    /**
     * (non-PHPdoc)
     * @see PHPExcel_Reader_IReadFilter::readCell()
     * @param int $column
     * @param int $row
     * @param string $worksheetName
     */
    public function readCell($column, $row, $worksheetName = '') { 
        //  Only read the heading row, and the configured rows 
        if (($row == 1) ||
            ($row >= $this->_startRow && $row < $this->_endRow)) { 
            return true; 
        } 
        return false; 
    } 
} 

