<?php
function smarty_modifier_status_display($value, $image = true)
{
	switch($value)
	{
		case Vanilla_Model_Row::STATUS_LIVE:
			$status = "icon-ok-sign";
			//$status = "live";
			break;
		case Vanilla_Model_Row::STATUS_PENDING:
			$status = "icon-question-sign";
			//$status = "pending";
			break;
		case Vanilla_Model_Row::STATUS_DELETED:
			$status = "icon-remove-sign";
			//$status = "deleted";
			break;
	}
	return getStatusHtml($status, $image);
}
/**
 * gets status html based on params
 * @param string $status
 * @param boolean $image
 * @return string
 */
function getStatusHtml($status, $image)
{
	if($image === true)
	{
		return '<i class="' . $status . '"></i>';
		//return '<img src="/admin/img/icons/status_'.$status.'.png" alt="'.ucwords($status).'" width="16" hegiht="16"/>';
	}
	return ucwords($status);
}