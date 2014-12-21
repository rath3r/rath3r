<?php
function smarty_function_text_area($params, &$smarty)
{
	$input_name  = $params['name'];
	$value       = isset($_POST[$input_name]) ? $_POST[$input_name] : $params['value'];
	$input_html  = '<input type="text" name="'.$input_name.'" value="'.$value.'" ';
	
	$input_html  = '<textarea name="'.$input_name;
	
	if($params['class'] !== null)
	{
		$input_html .= ' class="'.$params['class'].'" ';
	}
	
	$input_html  .= '">'.$value.'</textarea>';
	return $input_html;
}