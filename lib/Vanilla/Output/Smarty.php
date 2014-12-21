<?php

/**
 * Vanilla_Output_Smarty
 * Displays Smarty
 * 
 * @package    Vanilla
 * @subpackage Output
 * @author     Kasia Gogolek <kasia-gogolek@living-group.com>
 * @version    1.2
 */

include_once(BASE_DIR . LIB_FRAMEWORK_DIR . "Smarty/libs/Smarty.class.php");

class Vanilla_Output_Smarty extends Smarty {
	
	/**
	 * Initiate smarty with the passed set up 
	 * 
	 * @param Vanilla_Config_Ini $smarty_config Config object
	 * 
	 * @return void
	 */
	public function init($smarty_config = null)
	{
	    if($smarty_config === null)
	    {
	        $config        = new Vanilla_Config_Ini();
	        $smarty_config = $config->smarty;
	    }
        
        //$this->caching       = (bool) SMARTY_CACHE;
		$this->cache       = SMARTY_CACHE;
		$this->compile_dir = SMARTY_COMPILE_DIR;
		
		$this->initPlugins($smarty_config);
		$this->initDebug();
	}

	/**
	 * Init plugins directories
	 * @param array $smarty_config
	 * @return void
	 */
	public function initPlugins($smarty_config)
	{
//	    $this->setPluginsDir($smarty_config->plugins);
		foreach($smarty_config->plugins as $plugin)
		{
		    
			$this->plugins_dir[] = $plugin;
		}
	}
    
    /**
     * Add plugin dir
     * 
     * @return void
     */
    public function addPluginDir($dir)
    {
        $this->plugins_dir[] = $dir;
    }
    
	/**
	 * Initiate debug set up. Check if developer asked for it to be switched on
	 * @return void
	 */
	public function initDebug()
	{
		if ((defined('DEBUG_ENABLED') && DEBUG_ENABLED == true) && (defined('DEBUG_SMARTY') && DEBUG_SMARTY == true))
		{
			$this->debugging = true;
		}
		else
		{
			$this->debugging = false;
		}
	}

	/**
	 * Assign pagination to the view template
	 * @param Vanlla_Paginaton $pagination
	 * @return void
	 */
	public function setPagination(Vanilla_Pagination $pagination = null)
	{
		if($pagination !== null)
		{
		    /*if (empty($pagination->per_page_links))
		    {
		        $pagination->pagination();
		    }*/
			$this->assign('pagination', $pagination->pagination());
		}
	}
	
	/**
	 * Assign correct template directories
	 * @param array $template_dir_array
	 */
	public function setUpTemplateDirs(array $template_dir_array)
	{
		$this->smarty->template_dir = $template_dir_array;
	}
	
	/**
	 * Figure out the Smarty Template file name 
	 * based on forced template through $this->smarty_template
	 * or on action / controller name
	 * @param string $template_overwrite
	 * @param string $controller
	 * @param string $action
	 * @return string
	 */
	
	public function getSmartyTemplateFile($controller, $action, $template_overwrite = null, $language = null, $module = false)
	{
	    // if we're in a module, lets' reverse the template directories, as we want them to be searched first.
	    if($module === true)
	    {
	        $this->smarty->template_dir = array_reverse($this->smarty->template_dir);
	    }
	    
		if(null !== $template_overwrite)
		{
			return $template_overwrite;
		}
		
		$create_template_filename = false;
		$template_filename        = "";
		$path                     = explode("_", $controller);
		foreach($path as $value)
		{
			if($create_template_filename == true)
			{
				//changing ClassName to class-name for directories	
				$_tmp               = preg_replace('/([^A-Z-])([A-Z])/', '$1-$2', $value);
				$template_filename .= strtolower($_tmp) . "/";
			}
			if($value == "Controller")
			{
				$create_template_filename = true;
			} 
		}
		
		$tpl_name = $template_filename . substr($action, 0, strlen($action) - 6);
		
		if($language !== null && LANGUAGES_PREDEFINED == true)
		{
		    $tpl_name .= "_" . strtolower($language->iso);
		}
		
		$tpl_name .= ".tpl";
		
		$parent_controller = get_parent_class($controller);
		
        if($this->_checkTemplateExists($tpl_name))
        {
            return $tpl_name;
        }
		
        if($parent_controller == "Vanilla_Controller")
        {
            return $tpl_name;
        }
        
        
        $file =  $this->getSmartyTemplateFile($parent_controller, $action, null, null, true);
        
        return $file;
		
	}
	
	private function _checkTemplateExists($template_file)
	{
	    $template_dirs = $this->smarty->template_dir;
	    foreach($template_dirs as $dir)
	    {
	        if(file_exists($dir . $template_file))
	        {
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	
} 