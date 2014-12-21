<?php
function smarty_modifier_html_input($value, $input_name, $class_name = null)
{
	$value       = isset($_POST[$input_name]) ? $_POST[$input_name] : $value;
	$input_html  = '<input type="text" name="'.$input_name.'" value="'.$value.'" ';
	if($class_name !== null)
	{
		$input_html .= ' class="'.$class_name.'" ';
	}
	$input_html .= '/>';
	return $input_html;
}