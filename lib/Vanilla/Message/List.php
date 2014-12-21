<?php

/**
 * Message_List
 * Container class of message objects
 *
 * @name       Vanilla_Message_List
 * @category   Message
 * @package    Vanilla
 * @subpackage Message
 * @author     Suleman Chikhalia <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Message_List
 * Container class of message objects
 *
 * @name       Vanilla_Message_List
 * @category   Message
 * @package    Vanilla
 * @subpackage Message
 * @author     Suleman Chikhalia <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Message_List
{

    /**
     * Messages container that stores them as
     * An array
     * @var array
     */
    private $_messages = array();


    /**
     * Basic constructor
     */
    public function __construct() 
    {
        if (!empty($_SESSION['vanilla_messages']) && is_array($_SESSION['vanilla_messages']))
        {
            foreach ($_SESSION['vanilla_messages'] as $message_obj) {
                $this->_messages[] = unserialize($message_obj);
            }
            unset($_SESSION['vanilla_messages']);
        }
    }

    /**
     * Creates a new Message List instance
     *
     * @chainable
     * 
     * @return Vanilla_Message_List
     */
    public static function factory()
    {
        return new Vanilla_Message_List();
    }

    /**
     * Add message to the list
     * message can be a string or array
     * 
     * @param mixed  $message Message
     * @param string $type    Message Type
     * 
     * @return void
     */

    public function add($message, $type)
    {
        if ($message == null) {
            return null;
        }
        if (is_array($message)) {
            // this is an array, let's iterate over it
            $message_array = $message;
            
            foreach ($message_array as $message) {
                $this->_addOne($message, $type);
            }
        } else {
            $this->_addOne($message, $type);
        }
    }

    /**
     * Add one message to the list
     * 
     * @param Vanilla_Message $message Message
     * @param string          $type    Type
     * 
     * @return void
     */
    public function _addOne($message,  $type)
    {
        $message_obj       = new Vanilla_Message($type, $message);
        $this->_messages[] = $message_obj;
    }

    /**
     * Build error messages string for output
     * 
     * @param boolean $include_delimeters Include delimeters
     * 
     * @return string error string to output
     */
    public function fetch_all($include_delimeters = true)
    {
        $str  = "";
        if (!empty($this->_messages)) {
            foreach ($this->_messages as $message) {
                // skipping secret messages
                if ($message->message_type != Vanilla_Message::SYSTEM) {
                    $str .= $message->fetch($include_delimeters);
                }
            }
        }

        return $str;
    }
    /**
     * Fetching only secret messages
     * 
     * @return string secret string to output
     */
    public function fetch_system()
    {
        $str  = "";
        if (!empty($this->_messages)) {
            foreach ($this->_messages as $message) {
                // skipping secret messages
                if ($message->message_type == Vanilla_Message::SYSTEM) {
                    $str .= $message->fetch(false);
                }
            }
        }

        return $str;
    }

    /**
     * Checking if any error messages have been set
     * 
     * @return boolean
     */

    public function hasErrorMessages()
    {
        foreach ($this->_messages as $message) {
            if ($message->message_type == Vanilla_Message::ERROR) {
                return true;
            }
        }
        return false;
    }

} // End Message List