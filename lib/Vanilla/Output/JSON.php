<?php

/**
 * Vanilla_Output_JSON
 * Returns data as JSON
 * 
 * @package    Vanilla
 * @subpackage Output
 * @author     Kasia Gogolek <kasia-gogolek@living-group.com>
 * 
 */

class Vanilla_Output_JSON extends Vanilla_Output_BaseObject implements Vanilla_Interface_Output {
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Interface_Output::__toString()
	 * @return string
	 */
	public function __toString()
	{
		$response['response']         = $this->response;
		$response['response']['data'] = $this->data;
		return json_encode($response);
	}

} 