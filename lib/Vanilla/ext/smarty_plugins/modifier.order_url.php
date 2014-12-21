<?php
function smarty_modifier_order_url($title, $database_field, $flip = false)
{
	$get_array = $_GET;
	
	// if same database field, reverse order
	$get_array['order'] = (isset($get_array['field']) && $get_array['field']==$database_field) ? ($get_array['order']*-1):1;
	$get_array['field'] = $database_field;
	
	if(isset($get_array['action']))
	{
		unset($get_array['action']);
	}
    
    if($flip && !isset($_GET['order']))
    {
        $get_array['order'] *= -1;
    }
    
    if($flip && isset($_GET['field']) && $_GET['field'] != $database_field)
    {
        $get_array['order'] *= -1;
    }
	
	$query_path = http_build_query($get_array);
	$img        = getImage($get_array['order'], $database_field);
	
	$vanillaCurrentUrl = new Vanilla_Url();
    
    // add filter by
    $add_filter = '';
    if(isset($_POST['filter']))
    {
        $add_filter = '&filter=' . $_POST['filter'];
    }
    else if(isset($_GET['filter']))
    {
        $add_filter = '&filter=' . $_GET['filter'];
    }
    
    // add search text
    $add_search_text = '';
    if(isset($_POST['search_text']))
    {
        $add_search_text = '&search_text=' . $_POST['search_text'];
    }
    else if(isset($_GET['search_text']))
    {
        $add_search_text = '&search_text=' . $_GET['search_text'];
    }
    
    
    if(isset($_GET['field']) && $_GET['field'] == $database_field)
    {
        return "<a href='".$vanillaCurrentUrl->getPath()."?".$query_path.$add_filter.$add_search_text."'><span class=\"activeOrder\">".$title. $img. "</span></a>";
    }
    else
    {
        return "<a href='".$vanillaCurrentUrl->getPath()."?".$query_path.$add_filter.$add_search_text."'>".$title. $img. "</a>";
    }
}

function getImage($order, $databasefield)
{
    if((isset($_GET['field']) && $databasefield == $_GET['field']) || (!isset($_GET['field']) && $databasefield == "date"))
    {
    	if($order == 1)
    	{
            return '&nbsp;&#9660;';
    	}
    	else 
    	{
            return '&nbsp;&#9650;';
    	}
    }
    return null;
}