<?php
function smarty_modifier_page_access_display($pages_access_type_id, $pages_access_types)
{
	foreach($pages_access_types->rowset as $type)
	{
		if($type->id == $pages_access_type_id)
		{
			return $type->name;
		}
	}
	return null;
}