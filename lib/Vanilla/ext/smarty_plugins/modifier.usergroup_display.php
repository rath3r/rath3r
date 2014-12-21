<?php
function smarty_modifier_usergroup_display($user, $user_groups)
{
    $string = "";
    
    $user->_getUserGroupIdsArray();
    foreach($user_groups->rowset as $group)
    {
        if(in_array($group->id, $user->users_group_ids))
        {
            $data[] = $group->name;
        }
    }
    if(!empty($data))
    {
        $string = implode(", ", $data);
    }
    
	return $string;
}