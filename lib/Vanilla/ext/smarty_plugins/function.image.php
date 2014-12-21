<?php
function smarty_function_image($params, &$smarty)
{
	if(!isset($params['src']))
	{
		return null;
	}
	
	if(!file_exists(BASE_DIR . $params['src'] ))
	{
		$image_url = "http://placehold.it/". $params['width'] . "x" . $params['height'];
	}
	else 
	{
	    $image_url = Vanilla_Url::createURLFromRoute('media-view')
	        . "?path=/".$params['src']
	        . "&w=".$params['width']
	        . "&h=".$params['height'];
   
	}
	
	
	$html = '<img src="' . $image_url .'"'
	    . ' width="'. $params['width'] .'"'
	    . ' height="'. $params['height'] .'"';
     	
    if(isset($params['alt']))
    {
        $html .= ' alt="'. $params['alt'] .'"';
    }
     	
     if(isset($params['align']))
    {
        $html .= ' align="'. $params['align'] .'"';
    }
    
    if(isset($params['class']))
    { 	
     	$html .= ' class="'. $params['class'];
    }
    
    $html .= " />";
    
	return $html;
}