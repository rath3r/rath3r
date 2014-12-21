<?php

/**
 * Vanilla_Parser_HTML2_PDF
 * In the config settings make sure HTML2_OS is set to correct os
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

class Vanilla_Parser_HTML2_PDF extends Vanilla_Parser_HTML2
{
	
	public $lib_osx = "wkhtmltopdf-0.9.9-OS-X.i368";
	
	public $lib_linux = "wkhtmltopdf-i386";
	
	public $lib_win = "wkhtmltopdf.exe";
	
	public $lib_linux64 = "wkhtmltopdf-amd64";
	
	public $file_extension = "pdf";
	
	/**
	 * Parse HTML page to PDF
	 * 
	 * @param string $url       URL which we will turn into pdf
	 * @param string $pdf_title PDF Title 
	 * 
	 * @todo recognise the operating system 
	 * 
	 * @return void
	 */	
	public function parse($pdf_title, $return_file = true)
	{
	    
	    $this->setTitle2File($pdf_title);
	    if(!file_exists($this->tmp_file))
	    {
    		$this->getCorrectLibrary();
    		if(count($this->url) > 1)
    		{
    		    $this->concatFiles();
    		}
    		else 
    		{
    		    $this->getPDFFromUrl(end($this->url), $this->tmp_file);
    		}
	    }
		if($return_file === true)
		{
		    $this->returnFile();
		    die;
		}
		
		return $this->tmp_file;
		
	}
	
	/**
	 * Iterate through the url array, create PDF files in unique directory
	 * Then merge them into one and remove the tmp directory
	 * You will need to install pdftk to run this correctly
	 * 
         * @see        http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/
         * 
	 * @return void
	 * 
	 */
	public function concatFiles()
	{
	    $tmp_dir   = $this->getTMPDirBase() . uniqid();
	    $this->createDirectoryIfDoesntExist($tmp_dir);
	    
	    foreach($this->url as $key => $url)
	    {
	        $target_file =  $tmp_dir . DIRECTORY_SEPARATOR . str_pad($key, 5, '0', STR_PAD_LEFT) . "." . $this->file_extension;
	        $this->getPDFFromUrl($url, $target_file);
	    }
	    
    	$commandString = 'nice -n 19 ' . 'pdftk ' . $tmp_dir . '/*.pdf cat output ' . $this->tmp_file . ' 2>&1';
        $output       = shell_exec($commandString);
        if($output)
        {
            trigger_error($output);
            die;
        }
	}
	
	/**
	 * Clear Files
	 * 
	 * @param array  $files
	 * @param string $tmp_dir
	 * 
	 * @return void
	 */
	public function clearFiles($files, $tmp_dir)
	{
	    foreach($files as $file_path)
	    {
	        unlink($file_path);
	    }
	    rmdir($tmp_dir);
	}
	
	/**
	 * Get PDF file from URL
	 * 
	 * @param string $url         URL which we will scrape for PDF content
	 * @param string $target_file Target File that the PDF will be saved to
	 * 
	 * @return void
	 */
	public function getPDFFromUrl($url, $target_file)
	{
	    if(!file_exists($target_file))
        {
            if(HTML2_NOMARGINS == 1)
            {
                $margins = ' --margin-left 0 --margin-right 0 --margin-top 0 --margin-bottom 0';
            }
            elseif(HTML2_NOSIDEMARGINS == 1)
            {
                $margins = ' --margin-left 0 --margin-right 0';
            }
            else {
                $margins = '';
            }

            $url_array          = parse_url($url);
            $url_array['query'] = $this->addPrintVariableToQueryString($url_array['query']);
            
            $url = $url_array['scheme'] . "://" . $url_array['host'] . $url_array['path'] . "?" . $url_array['query'];
            $commandString = $this->lib .' --post username ' .HTML2_USER.' --post password ' .HTML2_PASSWORD. $margins . ' "' . $url . '" ' . $target_file . ' 2>&1';

            $output       = shell_exec($commandString);
        }
	}
	
	/**
	 * Adds the print = pdf to the end of the string
	 * 
	 * @param string $current_query Current Query string
	 * 
	 * @return void
	 */
	public function addPrintVariableToQueryString($current_query)
	{
	    if(empty($current_query))
        {
            $current_query .= "?print=pdf";
        }
        else
        {
            $current_query .= "&print=pdf";
        }    
	    return $current_query;
	}
	
	public function getFileFromUrl($url, $target_file)
	{
	    $this->getPDFFromUrl($url, $target_file);
	}
	
	/**
	 * Grabs the library specific to the operating system used
	 * @return string $lib_path
	 */
	public function getCorrectLibrary()
	{
		if(defined('PDF_LIB_PATH'))
		{
			$lib_path = PDF_LIB_PATH;			
		}
		else
		{ 
			$lib_path = getcwd(). DIRECTORY_SEPARATOR . LIB_FRAMEWORK_DIR . "Vanilla/ext/wkhtmltopdf/";
		}
		
		if(defined('HTML2_OS'))
		{
			// mac environment
			switch(HTML2_OS)
			{
				case "windows":
					$lib_path = "\"{$lib_path}{$this->lib_win}\"";
					break;
				case "mac":
					$lib_path .= $this->lib_osx;
					break;
				case "linux":
					$lib_path .= $this->lib_linux;
				case "linux64":
					$lib_path .= $this->lib_linux64;
					break;
			}
		}
		else 
		{
			// mac environment
			switch(APPLICATION_ENVIRONMENT)
			{
				case "development":
					$lib_path .= $this->lib_osx;
					break;
				case "client":
					$lib_path = '"C:\Program Files\wkhtmltopdf\\' . $this->lib_win.'"';
					break;
				case "staging":
				case "production":
					$lib_path .= $this->lib_linux;
					break;
			}
		}
		
		$this->lib = $lib_path;
		
	    return $this->lib;
	}
	
	/**
	 * Adding the site url to all images, css and js
	 * @param string $html
	 * @return string
	 */
	public function parseHtml($html)
	{
            
		$htmlDocument = DomDocument::loadHTML($html);
		$prepend      = "http://". $_SERVER['SERVER_NAME'];
		$htmlDocument = $this->prependTag($htmlDocument, 'img', 'src', $prepend);
		$htmlDocument = $this->prependTag($htmlDocument, 'script', 'src', $prepend);
		$htmlDocument = $this->prependTag($htmlDocument, 'link', 'href', $prepend, 'text/css');
		return $htmlDocument->saveHtml();
	}
	
	public function prependTag($htmlDocument, $tag, $attr, $prepend, $type = null)
	{
		$tags = $htmlDocument->getElementsByTagName($tag);
        foreach ($tags as $tag) 
        {
        	// check if the type is ok
        	if(null !== $type)
        	{
        		$tag_type = $tag->getAttribute('type');
        		if(!empty($tag_type) && $tag_type != $type)
        		{
        			continue;
        		}
        	}
			// get the current attribute value
			$attr_value = $tag->getAttribute($attr);
			
			// if empty go to next element
			if(empty($attr_value))
			{
				continue;
			}
			
			if(substr($attr_value, 0, 1) == "/")
			{
				// prepend with the set value
				$new_attr = $prepend. $attr_value;
				$tag->setAttribute($attr , $new_attr);
			}
		}
		return $htmlDocument;
	}
}