<?php
function smarty_modifier_file_type_display($value)
{
	if(preg_match("/^image\/.*$/", $value))
	{
		return '<img src="/admin/img/icons/mime-types/document-image.png" alt="Image" width="16" hegiht="16"/>';
	}
	if(preg_match("/^.*\/pdf$/", $value))
	{
		return '<img src="/admin/img/icons/mime-types/document-pdf.png" alt="PDF" width="16" hegiht="16"/>';
	}
	if(preg_match("/^application\/.*excel$/", $value))
	{
		return '<img src="/admin/img/icons/mime-types/document-excel.png" alt="Excel" width="16" hegiht="16"/>';
	}
	
	if(preg_match("/^application\/msword$/", $value))
	{
		return '<img src="/admin/img/icons/mime-types/document-word.png" alt="Word" width="16" hegiht="16"/>';
	}
	return '<img src="/admin/img/icons/mime-types/document.png" alt="Document" width="16" hegiht="16"/>';
	
}