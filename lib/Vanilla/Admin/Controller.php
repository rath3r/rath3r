<?php
/**
 * Admin Controller for all your admin needs
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @name Vanilla Admin Controller
 * @package Vanilla
 * @subpackage Admin
 * @abstract
 */

abstract class Vanilla_Admin_Controller extends Vanilla_Controller
{
	/**
	 * Content
	 * @todo what is this???? do we need it here? clean this up
	 * @var unknown_type
	 */
	public $content;
	
	/**
	 * Constructor
	 * @param string $controller
	 * @param string $action
	 * @param string $module
	 */
	public function __construct($controller = null, $action = null, $module = null, $page = null)
	{
		parent::__construct($controller, $action, $module, $page);
		$this->addJavascript('/admin/js/admin.js');
		$this->setAdminSettings();
	}
	
	protected function _getRelated()
{
$relationships = new Vanilla_Model_Relationship();
$entity_types = $relationships->getEntityTypes();
$this->smarty->assign('entity_types', $entity_types);
}
/**
* Checking if we want to add a relation to the object
* Add this function anywhere and include the relationships-list.tpl template
* in order to add the Relationships functionality
*/
protected function _addRelation()
{
if(isset($_POST['relate']))
{
unset($_POST['relate'], $_POST['find_relation']);
$relationship = new Vanilla_Model_Relationship();
$relationship->create($_POST);
if($relationship->id)
{
unset($_POST);
$this->addSuccessMessage("Relation added successfully");
}
}
}
protected function _deleteRelation()
{
if(isset($_GET['delete']))
{
$id = (int) $_GET['delete'];
$rel = new Vanilla_Model_Relationship();
$rel->getById($id);
$rel->delete();
}
}

public function enableRelations()
{
$this->_addRelation();
$this->_deleteRelation();
$this->_getRelated();
}
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Controller::preLoad()
	 */
	
	public function preLoad()
	{
		parent::preLoad(); 
		$this->addJavascript('/js/admin.js');
        $this->addJavascript('/js/libs/modernizr-2.5.3.min.js', 'prepend');
		$this->addStylesheet('/css/admin.css');
	}
		
	protected function _getOffices()
	{
		$offices        = new Vanilla_Model_Offices();
		$offices->getAll(null, null, null, 'name');
		$this->smarty->assign('offices', $offices);
	}
		
	protected function _getTeams()
	{
		$teams        = new Vanilla_Model_Teams();
		$teams->getAll(null, null, null, 'order');
		$this->smarty->assign('teams', $teams);
	}

	protected function _getEventTypes()
	{
		$eventTypes        = new Events_Model_Event_Types();
		$eventTypes->getAll(null, null, null, 'order');
		$this->smarty->assign('eventTypes', $eventTypes);
	}	
	protected function _getFileTypes()
	{
		$fileTypes        = new Model_FileTypes();
		$fileTypes->getAll(null, null, null);
		$this->smarty->assign('fileTypes', $fileTypes);
	}
			
	protected function _getFileCategories()
	{
		$fileCategories        = new Model_FileCategories();
		$fileCategories->getAll(null, null, null);
		$this->smarty->assign('fileCategories', $fileCategories);
	}
		
	public function setEntityGroupsFromConf($admin_type = null)
	{
	    $admin_type = strtolower($admin_type);
		$allowed_entities[$admin_type] = $this->admin_config->$admin_type;
		//$allowed_entities[$admin_type] = Vanilla_Admin::getAllowedEntities($admin_type);
		$this->smarty->assign('allowed_entities', $allowed_entities);
		if(count($allowed_entities[$admin_type])>0){
			foreach($allowed_entities[$admin_type] as $key => $item)
			{
				if ($item == true){
					$module = Vanilla_Admin::getModuleNameFromConf($key);
					if(null !== $module) // && Vanilla_Module::isInstalled($module)
					{
						$object = Vanilla_Admin::initAssignedModuleObject($module);
						$this->smarty->assign($module, $object->getAllLive());
					}
				}
			}
		}
	}
	
	public function addToParentEntityAction()
	{
		if(isset($_POST['parent_entity_type']) && isset($_POST['parent_entity_id']) && isset($_POST['child_entity_id']) && isset($_POST['child_entity_type']))
		{
			$entity = null;
			$entity_name = Vanilla_Model_Relationships::factory()->getObjectByType($_POST['child_entity_type'], true);
			$entity = $entity_name::factory();
			
			if ($entity != null)
			{
				$relation_class  = Vanilla_Model_Relationships::factory()->getObjectByType($_POST['parent_entity_type'], true);
				$relation_id = $relation_class::factory()->getById($_POST['parent_entity_id'])->addRelationship($_POST['child_entity_id'], $_POST['child_entity_type']);
				
				if ($relation_id != false) {

					$entity_array = $entity->getById($_POST['child_entity_id'])->toArray();
					//$entity_array['relation_id'] = $_POST['child_entity_id'];
					$entity_array['relation_id'] = $relation_id;
					$this->smarty->assign('entity', $entity_array);
					
				} else {
					
					$this->_ajaxError("This item has already been assigned.");
					
				}
				
			}
			else
			{
				$this->_ajaxError("There was an error adding this item.");
			}
		}
		else
		{
			$this->_ajaxError("There was an error adding this item.");
		}
	}
	
	/**
	 * Setting Up The template dir
	 * Adding the admin smarty path to the directories
	 * @param $template_dir 
	 * @todo once VanillaAdmin is redundant remove the second template directory
	 * @return void
	 */
	public function setUpMainTemplateDir()
	{
		$this->smarty->template_dir = array(getcwd() . "/". SMARTY_ADMIN_TEMPLATE_DIR);
	}
	
}