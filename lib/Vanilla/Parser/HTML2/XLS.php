<?php

/**
 * Vanilla_Parser_HTML2_XLS
 * This parser will expect the output in specific format i.e:
 * 
 * <root>
 *     <title>Title</title>
 *     <caption>Caption</caption>
 *     <headers>
 *         <header>Header 1</header>
 *     </headers>
 *     <headers>
 *         <header>Header 2</header>
 *     </headers>
 *     <items>
 *         <item>Item 1</item>
 *         <item>Item 2</item>
 *     </items>
 * </root> 
 * 
 * @name       Vanilla_Parser_HTML2_XLS
 * @category   Parser
 * @package    Vanilla
 * @subpackage Parser
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.1
 * @link       http://192.168.50.14/vanilla-doc/
 * @uses       Vanilla_Parser_HTML2, PHPExcel
 * 
 */

set_include_path(get_include_path() . PATH_SEPARATOR . LIB_FRAMEWORK_DIR . "Vanilla/ext/");
// Include PHP Powerpoint
require_once "PHPExcel.php";

/** PHPPowerPoint_IOFactory */
require_once 'PHPExcel/IOFactory.php';

/** PHPExcel_Writer_Excel2007 */
include 'PHPExcel/Writer/Excel2007.php';

/**
 * Vanilla_Parser_HTML2_XLS
 * This parser will expect the output in specific format i.e:
 * 
 * <root>
 *     <title>Title</title>
 *     <caption>Caption</caption>
 *     <headers>
 *         <header>Header 1</header>
 *     </headers>
 *     <headers>
 *         <header>Header 2</header>
 *     </headers>
 *     <items>
 *         <item>Item 1</item>
 *         <item>Item 2</item>
 *     </items>
 * </root> 
 *
 * @name       Vanilla_Parser_HTML2_XLS
 * @category   Parser
 * @package    Vanilla
 * @subpackage Parser
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.1
 * @link       http://192.168.50.14/vanilla-doc/
 * @uses       Vanilla_Parser_HTML2, PHPExcel
 */

class Vanilla_Parser_HTML2_XLS extends Vanilla_Parser_HTML2
{
    public $file_extension = "xls";
    
	/**
	 * Parse HTML to XLS
	 * 
	 * @param string $title Title of document returned
	 * 
	 * @return void
	 */	
	public function parse($title)
	{
            
        $this->setTitle2File($title);
		if(!file_exists($this->tmp_file))
		{
            if(count($this->url) > 1)
    		{
    		    $this->concatFiles();
    		}
    		else 
    		{
    		    $this->getXLSFromUrl(end($this->url), $this->tmp_file);
    		}
    		$this->returnFile();
		}
        else 
        {
            header('Content-Type: application/vnd.ms-excel');
    		header('Content-Disposition: attachment; filename='.basename($this->file_name . "." . $this->file_extension));
    		header('Cache-Control: max-age=0');
    		header('Content-type: application/vnd.ms-excel');
    		header('Content-Transfer-Encoding: binary');
    		header('Expires: 0');
    		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    		header('Pragma: public');
    		readfile($this->tmp_file);
        }        
		
		die;
	}
	
	/**
	 * Draw headers of a spreadhseet
	 * 
	 * @return void
	 */
	public function drawHeaders()
	{
	    foreach($this->data->headers as $headers)
	    {
    		$i = 1;
    		foreach($headers->header AS $header)
    		{
    			$col = $this->getColumnName($i);
    		    $this->objPHPExcel->getActiveSheet()->setCellValue($col.$this->row, (string) $header);
    			$this->objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
    			$this->objPHPExcel->getActiveSheet()->getStyle($col.$this->row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    			$this->objPHPExcel->getActiveSheet()->getStyle($col.$this->row)->getFill()->getStartColor()->setARGB('FFFFD400');
    	
    			$this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    			
    			$i++;
    		}
    		$this->row++;
	    }
		
	}
	
	/**
	 * Draw data in the spreadsheet
	 * 
	 * @return void
	 */
    public function drawData()
	{
	    $i = $this->row;
		foreach($this->data->items AS $items)
		{
			$j = 1;
			foreach($items AS $item)
			{
                $styles = $this->getStyles($item);

                $col = $this->getColumnName($j);
                
                //var_dump($col);
                $this->objPHPExcel->getActiveSheet()->setCellValue($col.$i, (string) $item);
                
                foreach($styles as $piece){
                    
                    $attr  = preg_replace('/-/', '_', substr($piece, 0, strpos($piece, ":")));
                    $func_name =  'add_' . $attr;
                    
                    if (method_exists($this,$func_name)) {
                        $this->$func_name($this->objPHPExcel->getActiveSheet(), $col, $i, $piece);
                    }
                }

				$j++;
			}
			$i++;
		}
		$this->row += (count($this->data->items));
        //die;
	}
	
    /**
     * get the styles from the attribute of the item tag 
     * 
     * @param object $item The SimpleXML object holding all the tag information 
     * 
     * @return array containing the different styles
     */
    public function getStyles($item)
    {
        unset($styles);
        foreach($item->attributes() as $a => $b) {
            if($a = 'style'){
                $styles = $b;
            }
        }
        return explode(";", $styles);
    }
    
    /**
     * add_background_color
     * 
     * @param object $obj The reference to the cell in the Excel object
     * @param int $col Int representing the current column
     * @param int $i Int representing the current cell
     * @param string $piece The string holding the key and value 
     */
    private function add_background_color($obj, $col, $i, $piece)
    {
        $bgcolor = '00' . preg_replace('/background-color:#/', '', $piece);

        $obj->getStyle($col.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $obj->getStyle($col.$i)->getFill()->getStartColor()->setARGB($bgcolor);
    }
    
    /**
     * add_color
     * 
     * @param object $obj The reference to the cell in the Excel object
     * @param int $col Int representing the current column
     * @param int $i Int representing the current cell
     * @param string $piece The string holding the key and value
     */
    private function add_color($obj, $col, $i, $piece)
    {
        $color = '00' . preg_replace('/color:#/', '', $piece);
        
        $obj->getStyle($col.$i)->getFont()->getColor()->setARGB($color);
    }
    
    /**
     * add_font_weight
     * 
     * @param object $obj The reference to the cell in the Excel object
     * @param int $col Int representing the current column
     * @param int $i Int representing the current cell
     * @param string $piece The string holding the key and value
     */
    private function add_font_weight($obj, $col, $i, $piece)
    {
        $font_weight = preg_replace('/font-weight:/', '', $piece);
        
        if($font_weight == 'bold'){
            $obj->getStyle($col.$i)->getFont()->setBold(true);
        }
    }
    
    /**
     * add_font_size
     *
     * @param object $obj The reference to the cell in the Excel object
     * @param int $col Int representing the current column
     * @param int $i Int representing the current cell
     * @param string $piece The string holding the key and value 
     */
    private function add_font_size($obj, $col, $i, $piece)
    {
        $font_size = preg_replace('/font-size:/', '', $piece);
        
        $obj->getStyle($col.$i)->getFont()->setSize($font_size);
    }
    
    /**
     * add_width
     *
     * @param object $obj The reference to the cell in the Excel object
     * @param int $col Int representing the current column
     * @param int $i Int representing the current cell
     * @param string $piece The string holding the key and value 
     */
    private function add_width($obj, $col, $i, $piece)
    {
        $width = preg_replace('/width:/', '', $piece);
        
        if($width == 'auto'){
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }
        else
        {
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($width);
            $this->objPHPExcel->getActiveSheet()->getStyle($col.$i)->getAlignment()->setWrapText(true);
        }
    }
    
	/**
	 * Draw Title
	 * 
	 * @return void
	 */
	public function drawTitle()
	{
	    $cols   = count($this->data->headers->header);
		$maxcol = $this->getColumnName($cols).'1';
		$this->objPHPExcel->getActiveSheet()->setCellValue('A1', (string) $this->data->caption);
		$this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		$this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF808080');
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
		$this->objPHPExcel->getActiveSheet()->mergeCells('A1:'.$maxcol);
	}
	
	/**
	 * Get XLS from the URL
	 * 
	 * @param string $url
	 * 
	 * @return void
	 */
	public function getXLSFromUrl($url, $file)
	{
	    $this->data = $this->parseUrlToXML($url);
		$this->row = 2;
		
		// Create new PHPExcel object
		$this->objPHPExcel = new PHPExcel();
		
		$this->objPHPExcel->getProperties()->setCreator(HTML2_CREATOR);
		$this->objPHPExcel->getProperties()->setLastModifiedBy(HTML2_LAST_MODIFIED_BY);
		$this->objPHPExcel->getProperties()->setTitle((string) $this->data->title);
		$this->objPHPExcel->getProperties()->setSubject((string) $this->data->caption);
		$this->objPHPExcel->getProperties()->setDescription((string) $this->data->caption);

		// Create a first sheet, representing sales data
		$this->objPHPExcel->setActiveSheetIndex(0);
		
		// $this->drawTitle();
		// $this->drawHeaders();
		
		$this->drawData();
		
		// Rename sheet
		$this->objPHPExcel->getActiveSheet()->setTitle();

		// Create a new worksheet, after the default sheet
		$this->objPHPExcel->createSheet();
		
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
		$objWriter->save($file);
	}
	
    public function getFileFromUrl($url, $target_file)
	{
	    $this->getXLSFromUrl($url, $target_file);
	}
	
	/**
	 * Return file 
	 * 
	 * @return void
	 */
	public function returnFile()
	{
	    if($this->file_extension == "zip")
	    {
	        parent::returnFile();
	    }
	    else 
	    {
    	    // Redirect output to a client’s web browser (Excel5)
    		header('Content-Type: application/vnd.ms-excel');
    		header('Content-Disposition: attachment; filename='.basename($this->file_name . "." . $this->file_extension));
    		header('Cache-Control: max-age=0');
    		header('Content-type: application/vnd.ms-excel');
    		header('Content-Transfer-Encoding: binary');
    		header('Expires: 0');
    		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    		header('Pragma: public');
    			
    		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
    		$objWriter->save('php://output');
	    }
	}
	
/**
	 * Get column name
	 * Transforms column number into a column letter
	 * 
	 * @param int $num
	 * 
	 * @throws Exception
	 * 
	 * @return string
	 */
	public function getColumnName($num)
	{
		// ensure the argument is sensible
		if ( ! is_numeric($num))
		{
			throw new Exception("num must be a number!", 1);
		}
		if ($num < 1)
		{
			throw new Exception("num must be greater than 0", 1);
		}
		$num--;
		for($s = ""; $num >= 0; $num = intval($num / 26) - 1)
		{
            $s = chr($num%26 + 0x41) . $s;
		}

		return $s;
	}
}