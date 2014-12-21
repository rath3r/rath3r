<?php

/**
 * Vanilla_Parser_HTML2_PDF
 * 
 * @name       Vanilla_Parser_HTML2_PDF
 * @category   Parser
 * @package    Vanilla
 * @subpackage Parser
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.1
 * @link       http://192.168.50.14/vanilla-doc/
 * @uses       Vanilla_Parser_HTML2
 * @uses       wkhtmltopdf
 * @uses       pdftk
 * @see        http://code.google.com/p/wkhtmltopdf/
 * @see        http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/
 * 
 */

/**
 * Vanilla_Parser_HTML2_PDF
 *
 * @name       Vanilla_Parser_HTML2_PDF
 * @category   Parser
 * @package    Vanilla
 * @subpackage Parser
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.1
 * @link       http://192.168.50.14/vanilla-doc/
 * @uses       Vanilla_Parser_HTML2
 * @uses       wkhtmltopdf
 * @uses       pdftk
 * @see        http://code.google.com/p/wkhtmltopdf/
 * @see        http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/
 */

class Vanilla_Parser_HTML2_JPG extends Vanilla_Parser_HTML2_PDF
{
	
	/**
	 * Parse HTML page to JPG
	 * 
	 * @param string $image_name JPG File name 
	 * 
	 * @return void
	 */	
	public function parse($image_name)
	{
	    $pdf_file   = parent::parse($image_name, false);
            $image_file = str_replace('.pdf', '.jpg', $pdf_file);
        
        if(defined('HTML2_JPG_CONVERT_PATH'))
        {
            $path = HTML2_JPG_CONVERT_PATH;
        }
        else 
        {
            $path = "/usr/bin/";
        }
        
        
        $commandString = 'nice -n 19 ' . $path . 'convert -resize "1000" -density 400 -quality 75 '.$pdf_file.' -trim '.$image_file;
        
        exec( $commandString.' 2>&1', $cmd_return );
        
        
        if(!empty($cmd_return))
        {
            Vanilla_Log::log($cmd_return);
        }
        
        unlink($pdf_file);
        
        return $image_file;
        
		
	}
	

}