<?php
function smarty_modifier_is_root($user)
{
	if($user instanceof Users_Model_User && $user->isRoot())
	{
		return true;
	}
	return false;
	
}