<?php
function smarty_function_link($params, &$smarty)
{
    $return_text  = null;
    $show_text    = getShowText($params);
    $query_string = getQueryString($params);
	$route_id     = $params['route_id'];
	$text         = getTextString($params);
	$class        = getClass($params);
	$rel          = getRel($params);
	
	if($show_text)
	{
	    $return_text = $text;
	}
	
	$route        = Vanilla_Router::getRoute($route_id);
	unset($params['route_id']);
	
	if(empty($route))
	{
	    return $return_text;
	}
	$page = Model_Page::factory()->createFromRoute($route, $route_id);
	$acl  = new Vanilla_ACL();
    $user = $acl->getLoggedUser();
	
	$url = Vanilla_Url::createURLFromRoute($route_id, $params);
	
    if(!Vanilla_ACL::hasEntityPermission($user, $page))
	{
	    return $return_text;
	}
	
	$html = '<a href="' . $url . $query_string . '" ' . $class . ' ' . $rel . '>' . $text . '</a>';
	return $html;
}

function getShowText($params)
{
    if(isset($params['show_text']) && $params['show_text'] == true)
    {
        return true;
    }
    return false;
}

function getQueryString($params)
{
    if(isset($params['query']))
    {
        $query_string = trim($params['query']);
        unset($params['query']);
        return $query_string;
    }
    return null;
}

function getTextString($params)
{
    if(isset($params['text']))
    {
        $string = $params['text'];
        unset($params['text']);
        return $string;
    }
    return null;
}

function getRel($params)
{
    if(isset($params['rel']))
    {
        $string = trim($params['rel']);
        unset($params['rel']);
        return 'rel="'. $string.'"';
    }
    return null;
}

function getClass($params)
{
    if(isset($params['class']))
    {
        $string = $params['class'];
        unset($params['class']);
        return 'class="'. $string.'"';
    }
    return null;
}