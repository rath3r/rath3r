<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */
 

function smarty_modifier_permission_check($group, $entity_types_id ,$entity_id)
{

	if (is_array($group->permissions))
	{
	   foreach($group->permissions as $item)
	   {
	   		
	   		if(
	   			($item['user_group_id'] == $group->id)&&
	   			($item['entity_types_id'] == $entity_types_id)&&
	   			($item['entity_id'] == $entity_id)
	   		) {
	
	   			return true;
	   		
	   		}
	   }
	}
	return false;
} 

?>