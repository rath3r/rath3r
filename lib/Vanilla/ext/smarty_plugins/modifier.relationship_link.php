<?php
function smarty_modifier_relationship_link($object)
{
    $url  = Vanilla_Url::createURLFromRoute('relationships');
    $url .= "?entity_id=".$object->id."&entity_type_id=".$object->entity_id;
    if(!empty($object->assign_entities))
    {
        $url .= "&child_entity_type_id=".implode(",", $object->assign_entities);
    }
    
    $html = '<a href="'. $url. '" class="btn btn-primary btn-small" rel="modal" >Add / Edit</a>';
    return $html;
}