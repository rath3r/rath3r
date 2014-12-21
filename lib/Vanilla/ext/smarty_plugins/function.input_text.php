<?php
function smarty_function_input_text($params, &$smarty)
{
	$input_name  = $params['name'];
	$value       = isset($_POST[$input_name]) ? $_POST[$input_name] : $params['value'];
	$input_html  = '<input type="text" name="'.$input_name.'" value="'.$value.'" ';
	if($params['class'] !== null)
	{
		$input_html .= ' class="'.$params['class'].'" ';
	}
	$input_html .= '/>';
	return $input_html;
}