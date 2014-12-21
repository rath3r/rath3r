<?php
function smarty_modifier_row_colour($value)
{
	if($value % 2 == 0)
	{
		return "light";
	}
	return "darker";
	
}