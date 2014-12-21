<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */
 

function smarty_modifier_object_permission_check($group, $object)
{
    $object->getUserPermissionsGroups();
    
    if(in_array($group->id, $object->users_permissions))
    {
        return true;
    }
    
    return false;
} 

?>