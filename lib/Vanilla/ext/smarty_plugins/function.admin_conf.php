<?php
function smarty_function_admin_conf($params, &$smarty)
{
	$section = $params['section'];
	$option  = $params['option'];
	$value   = $params['value'];
	
	return Vanilla_Admin::factory()->sectionOptionHasValue($section, $option, $value);
	
}