<?php
function smarty_function_user_name($params, &$smarty)
{
    if(isset($params['id']))
    {
    	$user_id = (int) $params['id'];
    	$user    = Model_User::factory()->getById($user_id);
    	if(isset($user->id) && $user->id > 0)
    	{
    	    return $user->first_name . " " . $user->last_name;
    	}
    }
	return null;
}