<?php
function smarty_modifier_pagegroup_display($page)
{
    $string = "";
    $user_groups = Users_Model_User_Groups::factory()->getAllLive();
    foreach($user_groups->rowset as $group)
    {
        
        if(!empty($page['users_permissions']) && in_array($group->id, $page['users_permissions']))
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