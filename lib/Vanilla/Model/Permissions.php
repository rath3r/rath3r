<?php

/**
 * @name Vanilla_Model_Relationships
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Permissions extends Vanilla_Model_Rowset
{
	/**
	 * Child entity type
	 * @var string
	 */
	public $child_entity_type;
	
	/**
	 * Getting all Permissions
	 * @param int $parent_id
	 * @param int $parent_entity_type_id
	 * @param string $child_entity_type
	 * @return array
	 */
	
	public function getAllPermissions($parent_id)
	{
		$_tmp = array();
		$db   = $this->getDAOInterface();
		$data = $db->getAllPermissions($parent_id);
		if(!empty($data))
		{
			foreach($data as $value)
			{
				$_tmp[] = ($value);
			}
		}
		return $_tmp;
	}
	
	/**
	 * Checking if User group has an entity permission
	 * @param Users_Model_User
	 * @param int $entity_id
	 * @param int $entity_type_id
	 * @return boolean
	 */
	public function hasEntityPermission($user, $entity_id, $entity_type_id)
	{
		
		if(empty($user->users_group_ids))
		{
			return false;
		}
		if(!empty($user) &&  ($user->users_type_id == Users_Model_User_Type::ADMIN_TYPE_ID || $user->users_type_id == Users_Model_User_Type::ROOT_TYPE_ID))
		{
			return true;
		}
		$_tmp = array();
		$db   = $this->getDAOInterface();
		$data = array(
			'users_group_ids'	=> $user->users_group_ids,
			'entity_id'			=> $entity_id,
			'entity_types_id'	=> $entity_type_id
		);
		
		$result = $db->checkPermissions($data);
		if(!empty($result))
		{
			return true;
		}
		return false;
	}
	
	
}