<?php
/**
 * Default Controller
 * @author Kasia Gogolek <kasia-gogolek@living-group.com>
 * @package HelloWorld
 *
 */
class Controller extends Vanilla_Controller
{
    public $track_on = true;

    /**
     * (non-PHPdoc)
     * @see Vanilla_Controller::preLoad()
     */
    public function preLoad()
    {
        $this->_addJSAndCSS();
        parent::preLoad();
        if (isset($_COOKIE["disclaimer"]) && $_COOKIE["disclaimer"] == 'yes') {
            $this->disclaimer_accepted = true;
            return;
        }

        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

    }

    public function __destruct()
    {
        $this->_checkDisclaimerAccepted();
        $this->_assignPageImage();
        parent::__destruct();
    }

    protected function _checkDisclaimerAccepted()
    {
        if(isset($_SESSION['disclaimer_accept']) || isset($_COOKIE['disclaimer_accept']))
        {
            setcookie("disclaimer", "yes");
            $this->smarty->assign('disclaimer_accepted', true);
            return;
        }
        $this->smarty->assign('disclaimer_accepted', false);
    }

    public static function _getAppCountryList()
    {
        $rows = Model_Countries::factory()->getAll(null, null, null, 'name');
        $countries = array();
        foreach ($rows->rowset as $country)
        {
            $countries[] = array(
                'id'	=> $country->id,
                'iso'	=> $country->iso,
                'name'	=> $country->name,
            );
        }

        return $countries;
    }

    protected function _assignPageImage()
    {
        $this->page->image = false;

        if (!empty($this->page->relations))
        {
            foreach ($this->page->relations as $entity) 
            {
                if ($entity->entity_id == Model_Image::ENTITY_ID)
                {
                    $this->page->image[] = $entity;
                    //break;
                }				
            }
        }

        if(count($this->page->image) > 0)
        {
            $this->smarty->assign('page_image', $this->page->image);
        }
        else
        {
            $this->smarty->assign('page_image', false);
        }
    }
    
    /**
     * Add and remove css and js at your discretion
     */
    protected function _addJSAndCSS() {
        $this->addStylesheet('/css/main.css');
        $this->addJavascript('/js/plugins.js');
        $this->addJavascript('/js/main.js');
    }	
    
    protected function _addAdminJSAndCSS() {
        $this->addStylesheet('/css/admin.css');
        $this->addStylesheet('/admin/css/smoothness/jquery-ui-1.8.16.custom.css');
        $this->addJavascript('/admin/js/jquery-ui-1.8.14.custom.min.js');
        $this->addJavascript('/admin/js/admin.js');
        $this->addJavascript('/js/admin.js');        
        $this->removeJavascript('/js/script.js');    
        $this->removeStylesheet('/css/style.css');
        $this->removeStylesheet('/admin/css/admin.css');
    }	

    /**
     * Get full navigation with all child / parent dependencies
     * Assigns the navigation to $this->navigation
     *
     * @return void
     */

    public function getNavigation()
    {
        try
        {
            if (is_object($this->page) && $this->page->parent_id === "admin") 
            {
                $this->navigation = new Vanilla_Navigation('admin', $this->url->getPath(), null, null, 1);
            }
            else
            {
                $this->navigation = new Vanilla_Navigation(0, $this->url->getPath(), null, null, 1);
            }
        }
        catch (Vanilla_Exception_MySQL $e)
        {
            return null;
        }
    }
}
