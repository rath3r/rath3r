<?php

/**
 * Vanilla_PHPMailer
 *
 * @name     Vanilla_PHPMailer
 * @category Mail
 * @package  Vanilla
 * @author   Gerard L. Petersen <freelance1@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.2
 * @link     http://192.168.50.14/vanilla-doc/
 */

require_once dirname(__FILE__).'/ext/PHPMailer/class.phpmailer.php';

/**
 * Vanilla_Parser
 *
 * @name     Vanilla_Parser
 * @category Mail
 * @package  Vanilla
 * @author   Gerard L. Petersen <freelance1@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.2
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_PHPMailer extends PHPMailer
{

    /**
     * Setting smarty class here
     * @var Smarty
     */
    public $smarty;

    /**
     * Setting smarty template file for password reminder
     * @var string
     */
    public $template;

    
    /**
     * Constructor
     * 
     * @param boolean $exceptions Should we throw external exceptions?
     * 
     * @magic
     * 
     * @return void
     */
    public function __construct($exceptions = false)
    {
        parent::__construct($exceptions);

        if (EMAIL_USE_SMTP) {
            $this->IsSMTP();
            if (EMAIL_HOST) {
                $this->Host = EMAIL_HOST;
            }
            
            if (EMAIL_SMTP_AUTH) {
                $this->SMTPAuth = true;
            }
            
            if (EMAIL_SMTP_SECURE) {
                $this->SMTPSecure = "ssl";
            }
            
            if (EMAIL_PORT) {
                $this->Port = EMAIL_PORT;
            }
        }
        
        if (defined('EMAIL_USERNAME')) {
            $this->Username = EMAIL_USERNAME;
        }
        
        if (defined('EMAIL_PASSWORD')) {
            $this->Password = EMAIL_PASSWORD;
        }

        $this->SetFrom(EMAIL_FROM, EMAIL_FROM_NAME ? EMAIL_FROM_NAME : '');
    }
    
    public function getError()
    {
        return $this->ErrorInfo;
    }

    public function setSubject($subject)
    {
        $this->Subject = $subject;
    }
    
    
    /**
     * Set template
     * 
     * @param string $template_file Template File
     * 
     * @return Smarty
     */
    public function setTemplate($template_file)
    {
        $this->_initSmarty();
        $this->template = $template_file;
        return $this->smarty;
    }

    /**
     * Init Smarty
     * 
     * @return void
     */
    private function _initSmarty()
    {
        @include_once('Smarty.class.php');
        $this->smarty               = new Smarty();
        $this->smarty->compile_dir  = SMARTY_COMPILE_DIR;
    }

    /**
     * Checking if need to initiate a Smarty template
     * (non-PHPdoc)
     * 
     * @see PHPMailer::Send()
     * 
     * @return void
     */
    public function Send()
    {
        if ($this->template !== null) {
            $this->smarty->template_dir = SMARTY_TEMPLATE_DIR;
            $this->Body =  $this->smarty->fetch("_emails/".$this->template);
        }
        if (defined("DEBUG_EMAIL") && DEBUG_EMAIL == true) {
            $this->AddBCC(EMAIL_DEVELOPER, "Debug Email");
        }
        return parent::Send();
    }
}