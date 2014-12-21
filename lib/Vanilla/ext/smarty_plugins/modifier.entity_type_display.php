<?php
function smarty_modifier_entity_type_display($entity_id, $entity_types)
{
	foreach($entity_types->rowset as $type)
	{
		if($type->id == $entity_id)
		{
			return $type->name;
		}
	}
	return null;
}