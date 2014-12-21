<?php 

/**
 *  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter 
 *  @name Vanilla_Parser_Excel_ChunkFilter
 *  @package Vanilla
 *  @subpackage Parser
 *  @author kgogolek
 *  
 */

class Vanilla_Parser_Excel_ChunkFilter implements PHPExcel_Reader_IReadFilter 
{ 
	/**
	 * Specify start row
	 * @var int
	 */
    private $_startRow = 0; 
    
    /**
	 * Specify end row
	 * @var int
	 */
    private $_endRow   = 0; 

    /**
     * 
     * Set the list of rows that we want to read 
     * @param int $startRow
     * @param int $chunkSize
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

