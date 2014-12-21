<?php

require_once LIB_FRAMEWORK_DIR . 'Vanilla/ext/FPDF/fpdf.php';
require_once LIB_FRAMEWORK_DIR . 'Vanilla/ext/FPDF/fpdi.php';


/**
 * Utilises the FPDF and FPDI extension to allow users to add a Watermark to a PDF
 * 
 * @name Vanilla_PDF_Watermark
 * @author Niall St John <niall.stjohn@living-group.com>
 * @todo Currently does not calculate size of source pdf file assumes A4
 * @package	Vanilla
 * @subpackage	PDF
 * 
 */
class Vanilla_PDF_Watermark {
	
	/**
	 * 
	 * Enter description here ...
	 * @var Vanilla_PDF_FPDI
	 */
	public $pdf;
	
	/**
	 * The name of the PDF file
	 * @var string
	 */
	private $filename = '';
	
	/**
	 * The source of the PDF file, including full path
	 * @var string
	 */
	private $sourcefile = '';

	/**
	 * The text to call the new PDF file, once watermarked
	 * @var string
	 */
	private $watermarked_filename = '';
	
	/**
	 * Total number of pages in the PDF
	 * @var int
	 */
	private $numPages = 1;
	
	/**
	 * The font to write the watermark in
	 * @var string
	 */
	private $font = 'Arial';
	
	/**
	 * The font size to write the watermark in
	 * @var int
	 */
	private $font_size = 12;
	
	/**
	 * The red part of RGB for the text colour
	 * @var int
	 */
	private $font_colour_r = 0;
	
	/**
	 * The green part of RGB for the text colour
	 * @var int
	 */
	private $font_colour_g = 0;
	
	/**
	 * The blue part of RGB for the text colour
	 * @var int
	 */
	private $font_colour_b = 0;
	
	/**
	 * The text for the watermark
	 * @var string
	 */
	private $text = '';
	
	/**
	 * The X position of the watermark, ie how far across the page
	 * @var int
	 */
	private $posX = -5;
	
	/**
	 * The Y position of the watermark, ie how far down the page
	 * @var int
	 */
	private $posY = -5;
	
	/**
	 * The size of the PDF document, ie width and height
	 * @var int
	 * @todo This is not yet being calculated correctly
	 */
	private $size;
	
	/**
	 * I'm holdin all the params for all of the watermarks in here
	 * @var array
	 */
	private $params;
	
	/**
	 * Construct
	 * @param array $params
	 */
	public function __construct(array $params = array()) {
		
		/*
		 * Initialise the FPDI object
		 */
		$this->pdf = new Vanilla_PDF_FPDI();
		/*
		 * Add params
		 */
		$this->addParams($params);
		
		/*
		 * Set source file
		 */
		$this->setSourceFile();
		
	}
	
	/**
	 * Add the parameters
	 * @param array $params
	 */
	private function addParams(array $params) {
		foreach ($params as $name => $val) {
			$this->$name = $val;
		}
	}
	
	/**
	 * Add text specific params
	 * @param array $params
	 * @chainable
	 * @return Vanilla_PDF_Watermark
	 */
	public function addTextParams($params)
	{
		if (is_array($params)) {
			$this->text_params[] = $params;
		}
		return $this;
	}
	
	/**
	 * Set the source PDF file
	 */
	private function setSourceFile() {
		$this->numPages = $this->pdf->setSourceFile($this->sourcefile);
	}
	
	/**
	 * Import the source PDF file
	 * This is done by taking each individual page as an image and then placing onto a blank page in the new PDF
	 */
	private function importPDF() {
		
		$width = 0;
		$width = (int) floor($this->pdf->w);
		
		/*
		 * For each page of the source pdf, get a snapshot
		 * and place onto the corresponding page of the new
		 * pdf
		 */
		for ($i = 1; $i <= $this->numPages; $i++) {

			/*
			 * Create base pdf page
			 */
			$this->pdf->AddPage();
			
			/*
			 * Import page 1
			 */
			$tplIdx = $this->pdf->importPage($i);
			
			/*
			 * This places the image onto the new blank page
			 */
			$posX = 0;
			$posY = 0;
			$this->size = $this->pdf->useTemplate($tplIdx, $posX, $posY, $width -10);
			$this->writeWatermark();
		}
		
	}
	
	/**
	 * Writes the watermark onto the new PDF
	 * This is done for each page
	 */
	private function writeWatermark() {
		
		foreach($this->text_params as $params)
		{
			$params = $this->calculatePosition($params);
			
			$this->pdf->SetFont($params['font']);
			$this->pdf->SetFontSize($params['font_size']); 
			$this->pdf->SetTextColor($params['font_colour_r'],$params['font_colour_g'],$params['font_colour_b']); 
			
			if(isset($params['rotate']))
			{
				$this->pdf->SetAlpha(0.5);
				$this->pdf->rotate($params['rotate'], $params['posX'], $params['posY']);
				$this->pdf->SetXY($params['posX'], $params['posY']);
				$this->pdf->MultiCell(120,4, $params['text'], 0, "C");
				$this->pdf->rotate(0);
				$this->pdf->SetAlpha(1);
			}
			else 
			{
				$this->pdf->SetXY($params['posX'], $params['posY']); 
				$this->pdf->Write(-10, $params['text']); 
			}
		}
	}
	
	
	/**
	 * Calculate position for the watermark
	 * @param array $params
	 * @return void
	 */
	private function calculatePosition(array $params) {
		
		if(isset($params['pos']))
		{
			/*
			 * Get dimensions
			 */
			$width = floor($this->size['w']);
			$height = floor($this->size['h']);
			
			/*
			 * Calculate position
			 */
			switch ($params['pos']) {
				case 'top_left':
					$params['posX'] = 10;
					$params['posY'] = 10;
					break;
				case 'bottom_left':
					$params['posX'] = 10;
					$params['posY'] = $height - 25;
					break;
			}
		}
		return $params;
	}
	
	/**
	 * Outputs the watermarked pdf
	 * This can only be executed if no headers have already been sent
	 */
	public function output() {
		$this->importPDF();
		$this->pdf->Output($this->filename, 'D');
	}
}