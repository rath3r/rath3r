<?php
/**
 * Smarty plugin
 *
 * @package Vanilla
 * @subpackage PluginsModifier
 */
 

function smarty_modifier_has_permission(Users_Model_User $user, $entity_types_id, $entity_id)
{
	$permissions = $user->getGroupPermissions(null,true);
	if($user->isAdmin() || isset($permissions[$entity_types_id]) && isset($permissions[$entity_types_id][$entity_id]))
	{
		return true;
	}
	
	return false;
} 

?>