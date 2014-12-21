<?php

/**
 * Vanilla_Locale
 *
 * @name     Vanilla_Locale
 * @category Locale
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Locale
 *
 * @name     Vanilla_Locale
 * @category Locale
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Locale
{
    private $_language;
    private $_region;
    
    /**
     * Construct magic method
     * 
     * @param string $locale_iso LOCALE ISO
     * 
     * @return Vanilla_Locale
     */
    public function __construct($locale_iso = null)
    {
        if($locale_iso !== null)
        {
            $this->setLocale($locale_iso);
        }
    }
    
    /**
     * Register Session with this object
     * 
     * @return void
     */
    public function registerSession()
    {
        $session_key            = $this->_getSessionKey();
        $_SESSION[$session_key] = serialize($this);
    }
    
    public static function removeSession()
    {
        $session_key = self::_getSessionKey();
        unset($_SESSION[$session_key]);
    }
    
    /**
     * Check if locale has been set
     * 
     * @return boolean
     */
    public static function hasBeenSet()
    {
        $session_key   = self::_getSessionKey();
        if(isset($_SESSION[$session_key]))
        {
            $locale = self::getFromSession();
            if($locale->getLanguage() !== null || $locale->getRegion() !== null)
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get Object from session if it exists
     * 
     * @return Vanilla_Locale
     */
    public static function getFromSession()
    {
        $session_key   = self::_getSessionKey();
        if(isset($_SESSION[$session_key]))
        {
            $locale_object = unserialize($_SESSION[$session_key]);
            if($locale_object instanceof Vanilla_Locale)
            {
                return $locale_object;
            }
        }
        return new self;
    }
    
    /**
     * Get session Key specific for this class
     * 
     * @return string
     */
    private function _getSessionKey()
    {
        $session_key = "vanillaLocale" . md5(APP_NAME);
        return $session_key;
    }
    
    /**
     * Set Locale
     * 
     * @param string $locale_iso Locale ISO
     * 
     * @return Vanilla_Locale
     */
    public function setLocale($locale_iso)
    {
        $_tmp = explode("_", $locale_iso);
        $this->_language = strtolower($_tmp[0]);
        if(!isset($_tmp[1]) || empty($_tmp[1]))
        {
            $_tmp[1] = strtoupper($_tmp[0]);
        }
        $this->_region   = $_tmp[1];
        return $this;
    }
    
    public function getISO()
    {
        return $this->_language."_".$this->_region;
    }
    
    /**
     * Get Language Object
     * 
     * @return Languages_Model_Language
     */
    public function getLanguageObject()
    {
        if($this->language === null)
        {
            if(Vanilla_Module::isInstalled("Languages"))
            {
                $language_iso   = $this->getLanguage();
                $params         = array('iso' => strtoupper($language_iso), 'status' => Vanilla_Model_Row::STATUS_LIVE);
                $languages      = Languages_Model_Languages::factory()->getAll(null, null, $params);
                $this->language = current($languages->rowset);
                if($this->language->id == 0)
                {
                    $this->language = null;
                }
                $this->registerSession();
            }
        }
        return $this->language;
    }
    
    /**
     * Get Language 
     * 
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }
    
    /**
     * Get Region that has been set
     * 
     * @return string
     */
    public function getRegion()
    {
        return $this->_region;
    }
    
}