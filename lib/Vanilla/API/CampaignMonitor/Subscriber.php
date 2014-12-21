<?php

/**
 * Campaign Monitor Subscriber API Connector
 * Use this function for easy connection to the API
 *
 * @name       Vanilla API Vimeo
 * @category   API
 * @package    Vanilla
 * @subpackage API
 * @author     Niall St John <niall.stjohn@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Campaign Monitor Subscriber API Connector
 * Use this function for easy connection to the API
 *
 * @name       Vanilla API Vimeo
 * @category   API
 * @package    Vanilla
 * @subpackage API
 * @author     Niall St John <niall.stjohn@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

class Vanilla_API_CampaignMonitor_Subscriber
{

    public function __construct($api_key)
    {
        require_once LIB_VANILLA_DIR . '/ext/campaignmonitor/csrest_subscribers.php';
        $this->api_key = $api_key;
    }

    
    
    public function addEmailToCMList($list_id, $data)
	{
	    $wrap = new CS_REST_Subscribers($list_id, $this->api_key);
        $result = $wrap->add(array(
            'EmailAddress' => $data['email'],
            'Name' => $data['name'],
            /*'CustomFields' => array(
                array(
                    'Key' => 'Field 1 Key',
                    'Value' => 'Field Value'
                ),
                array(
                    'Key' => 'Field 2 Key',
                    'Value' => 'Field Value'
                ),
                array(
                    'Key' => 'Multi Option Field 1',
                    'Value' => 'Option 1'
                ),
                array(
                    'Key' => 'Multi Option Field 1',
                    'Value' => 'Option 2'
                )
            ),*/
            'Resubscribe' => true
        ));
        
        $response = true;
        
        if($result->was_successful()) {
            //echo "Subscribed with code ".$result->http_status_code;
        } else {
            $errors[] = "Result of POST /api/v3/subscribers/{list id}.{format}";
            $errors[] = 'Failed with code '.$result->http_status_code;
            $errors[] = $result->response->Message;
            $errors[] = "List ID: " . $list_id; 
            $response = $errors; 
        }
        
        return $response;
	    
	}
    
}