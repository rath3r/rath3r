<?php

/**
 * Vanilla_Pagination
 *
 * @name     Vanilla_Pagination
 * @category Navigation
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Pagination
 *
 * @name     Vanilla_Pagination
 * @category Navigation
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Helper 
{
    public $smarty;
    
    public $template = "";

    
	/**
     * Init Smarty
     * 
     * @return void
     */
    public function initSmarty()
    {
        require_once(BASE_DIR . LIB_FRAMEWORK_DIR . 'Smarty/libs/Smarty.class.php');
        $this->smarty               = new Smarty();
        
        $this->smarty->compile_dir  = SMARTY_COMPILE_DIR;
        $this->smarty->template_dir = SMARTY_TEMPLATE_DIR;
        $this->smarty->debugging     = false;
    }
    
    
    public function setTemplate($template_file)
    {
         $this->template = $template_file;
         
    }
    
    public function __toString()
    {
        $this->initSmarty();
        return $this->smarty->fetch($this->template);
        
    }

}