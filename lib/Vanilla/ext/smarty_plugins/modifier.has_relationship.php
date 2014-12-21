<?php
function smarty_modifier_has_relationship($relations, $entity_id, $entity_type_id)
{
    if(!empty($relations))
    {
    	foreach($relations as $relation)
    	{
    		if($relation->id == $entity_id && $relation->entity_id == $entity_type_id)
    		{
    			return true;
    		}
    	}
    }
	return false;
}
