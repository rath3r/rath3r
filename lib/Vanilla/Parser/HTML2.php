<?php

/**
 * Vanilla_Parser_HTML2
 * Vanilla HTML2 PDF/PPT/XLS Parser
 * this parser requires it's own config section with set up as per below
 * 
 * [html2]
 * user     = "USER_EMAIL"
 * password = "USER_PASSWORD"
 * creator  = "CREATOR NAME"
 * last_modified_by = "LAST_MODIFIED_BY"
 * 
 * user and password settings will be used to log onto the page where we'll grab the data from.
 * creator and last modified by will be used in Excel and PPT generation, as part of default 
 * Microsoft set up
 *
 * @name       Vanilla_Parser_HTML2
 * @category   Parser
 * @package    Vanilla
 * @subpackage Parser
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.1
 * @link       http://192.168.50.14/vanilla-doc/
 * @used-by    Vanilla_Parser_HTML2_PDF, Vanilla_Parser_HTML2_PPT, Vanilla_Parser_HTML2_XLS
 * 
 */

/**
 * Vanilla_Parser_HTML2
 * Vanilla HTML2 PDF/PPT/XLS Parser
 * this parser requires it's own config section with set up as per below
 * 
 * [html2]
 * user     = "USER_EMAIL"
 * password = "USER_PASSWORD"
 * creator  = "CREATOR NAME"
 * last_modified_by = "LAST_MODIFIED_BY"
 * 
 * user and password settings will be used to log onto the page where we'll grab the data from.
 * creator and last modified by will be used in Excel and PPT generation, as part of default 
 * Microsoft set up
 *
 * @name       Vanilla_Parser_HTML2
 * @category   Parser
 * @package    Vanilla
 * @subpackage Parser
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.1
 * @link       http://192.168.50.14/vanilla-doc/
 * @used-by    Vanilla_Parser_HTML2_PDF, Vanilla_Parser_HTML2_PPT, Vanilla_Parser_HTML2_XLS
 */

class Vanilla_Parser_HTML2 extends Vanilla_Parser
{
    
    public $tmp_dir = "tmp";
	
	public $url;
	
	public $file_name;
	
	public $skip_timestamp = false;
	
	
	/**
	 * Add URLs we will parse
	 * 
	 * @param array $url Array of URLs or a URL string
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Parser_HTML2
	 */
	public function setUrls(array $url)
	{
            foreach($url as &$link)
            {
                $link = Vanilla_Url::getHostName(). $link;
            }
	    $this->url = $url;
	    return $this;  
	}
	
	/**
	 * Change title to a file name
	 * 
	 * @param string $title Title
	 * 
	 * @return void
	 */
	public function setTitle2File($title)
	{
	    $search   = array(" ","&","/");
		$replace  = array("_","and","-");
		$this->file_name = str_replace($search, $replace, strtolower($title));
		$this->setTmpFile();
	}
	
	/**
	 * Create directory if it doen't exist
	 * 
	 * @param string $dir
	 * 
	 * @return void
	 */
	public function createDirectoryIfDoesntExist($dir)
	{
	    if(!file_exists($dir))
	    {
	        mkdir($dir);
	    }
	}
	
	/**
	 * Parse HTML to XML
	 * 
	 * @param string $url URL we will parse
	 * 
	 * @todo incorporate DOM class?
	 * 
	 * @return SimpleXMLElement
	 */
    public function parseUrlToXML($url)
	{
	    $html = $this->getHTMLFromUrl($url);
	    $find = array('&nbsp;', "&");
		$replace = array('', " and " );
		$html = str_replace($find,$replace,$html);
		$html = preg_replace("/\h{2,}/","",$html);
	    $html = str_replace(chr(11), '', $html);
		if (!simplexml_load_string($html)){
    		
		    $errors = libxml_get_errors();
		    foreach($errors as $error) {
                var_dump ($error->message);
            }
            
            var_dump(libxml_get_last_error());
    
		    print 'error creating document';
		    print '-----------------------';
		    print('<pre>');
    	    print(htmlentities($html));
    	    print('</pre>');
		} else {
		    return simplexml_load_string($html);
		}
	}
	
	/**
	 * Iterate through the url array, create PPT files in unique directory
	 * Then merge them into one and remove the tmp directory
	 * You will need to install pdftk to run this correctly
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
	        $target_file =  $tmp_dir . DIRECTORY_SEPARATOR . $this->titles[$key] . "." . $this->file_extension;
	        $this->getFileFromUrl($url, $target_file);
	        $files[] = $target_file;
	    }
	    $this->zipFiles($files);
	}
	
	/**
	 * Set file titles 
	 * 
	 * @param array $titles Titles for each file
	 * 
	 * @chainable
	 * 
	 * @return Vanilla_Model_Parser_HTML2
	 */
	public function setFileTitles(array $titles)
	{
	    $search   = array(" ","&","/");
		$replace  = array("_","and","-");
	    foreach($titles as &$title)
	    {
	        $title = str_replace($search, $replace, strtolower($title));
	    }
	    $this->titles = $titles;
	    return $this;
	}
	
	public function skipTimestamp()
	{
	    $this->skip_timestamp = true;
	    return $this;
	}
	
	public function zipFiles($files)
	{
	    $zip = new ZipArchive();
	    $zip_destination = $this->getZipTmpFile();
        $zip->open($zip_destination, ZIPARCHIVE::CREATE);
        
        //add the files
        foreach($files as $file)
        {
            $_tmp = explode(DIRECTORY_SEPARATOR, $file);
            $zip->addFile($file, end($_tmp));
        }
        $zip->close();
	}
	
	public function getZipTmpFile()
	{
	    $file_name_len  = strlen($this->tmp_file);
	    $ext_name_len   = strlen($this->file_extension);
	    $start          = $file_name_len - $file_name_len;
	    $this->tmp_file = strtr($this->tmp_file,  $start, $ext_name_len). ".zip";
	    $this->file_extension = "zip";
	    return $this->tmp_file;
	}
	
	/**
	 * Get the TMP file path and copy the content of html to it
	 * 
	 * @return void
	 */
	public function setTmpFile()
	{
	    if(!defined("LIB_CACHE_DIR"))
	    {
	        trigger_error("Can't use this library until you specify LIB_CACHE_DIR in your production.ini");
	    }
		
	    $prepend = null;
	    
	    if($this->skip_timestamp === false)
	    {
	        $prepend = time(). "_";
	    }
	    
		$tmp_file = $this->getTMPDirBase() . $prepend . $this->file_name . "." . $this->file_extension;
		$this->tmp_file = $tmp_file;
	}
	/**
	 * Get TMP dir for storing files
	 * 
	 * @return string
	 */
	public function getTMPDirBase()
	{
	    $tmp_dir = getcwd() . DIRECTORY_SEPARATOR . LIB_CACHE_DIR . $this->tmp_dir . DIRECTORY_SEPARATOR;
	    return $tmp_dir;
	}
	
	/**
	 * Crawler to get the HTML from URL
	 * 
	 * @param string $url
	 * 
	 * @return string
	 */
	
    public function getHTMLFromUrl($url)
	{
	    $curl = curl_init();
        // Setup headers - I used the same headers from Firefox version 2.0.0.6
        // below was split up because php.net said the line was too long. :/
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 12000";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";
        // browsers keep this blank.
     
        $referers = array("google.com", "yahoo.com", "msn.com", "ask.com", "live.com");
        $choice = array_rand($referers);
        $referer = "http://" . $referers[$choice] . "";
     
        $browsers = array("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092510 Ubuntu/8.04 (hardy) Firefox/3.0.3", "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20060918 Firefox/2.0", "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3", "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)");
        $choice2 = array_rand($browsers);
        $browser = $browsers[$choice2];
     
        $post_query = "username=" . HTML2_USER . "&password=" . HTML2_PASSWORD . "&print=".$this->file_extension;
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_query);
        curl_setopt($curl, CURLOPT_USERAGENT, $browser);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
     
        $data = curl_exec($curl);
     
        if ($data === false) 
        {
            $data = curl_error($curl);
        }
     
        // execute the curl command
        curl_close($curl);
        // close the connection
     
        return $data;
	}
	/**
	 * Return file to the user that requested it
	 * 
	 * @return void
	 */
    public function returnFile()
	{
        if($this->file_extension == "ppt"){
            $this->file_extension = "pptx";
        }
                
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($this->file_name."." .$this->file_extension));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($this->tmp_file));
		ob_clean();
	    flush();
	    readfile($this->tmp_file);
	}
}