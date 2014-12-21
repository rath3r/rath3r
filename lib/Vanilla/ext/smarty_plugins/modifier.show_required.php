<?php
function smarty_modifier_show_required($required_fields, $field)
{
	if(!empty($required_fields) && in_array($field, $required_fields))
	{
		return '<span class="red strong">&nbsp;*&nbsp;</span>';
	}
	return;
	
}