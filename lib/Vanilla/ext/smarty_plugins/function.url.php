<?php
function smarty_function_url($params, &$smarty)
{
	if(isset($params['route_id']))
    {
        $route_id = $params['route_id'];
        unset($params['route_id']);
        return Vanilla_Url::createURLFromRoute($route_id, $params);
    }
    if(isset($params['page_id']))
    {
        $id   = (int) $params['page_id'];
        $page = new Model_Page;
        $page->getById($id);
        return $page->full_url;
    }
}