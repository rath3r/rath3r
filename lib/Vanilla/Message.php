<?php
/**
 * Message - Stores a message string with it's type
 *
 * @name     Vanilla_Exception
 * @category Exception
 * @package  Vanilla
 * @author   Suleman Chikhalia <freelance1@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Message - Stores a message string with it's type
 *
 * @name     Vanilla_Exception
 * @category Exception
 * @package  Vanilla
 * @author   Suleman Chikhalia <freelance1@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Message
{

    /**
     * ERROR String
     */
    const ERROR = 'ERROR';

    /**
     * SUCCESS String
     */
    const SUCCESS = 'SUCCESS';

    /**
     * NOTICE String
     */
    const NOTICE = 'NOTICE';

    /**
     * MESSAGE String
     */
    const MESSAGE = 'MESSAGE';
    
    /**
     * WARNING String
     */
    const WARNING = 'WARNING';

    /**
     * SECRET String
     */
    const SYSTEM = 'SYSTEM';

    /**
     * Message
     * @var string
     */
    public $message;

    /**
     * Message type
     * @var string
     */
    public $message_type;

    /**
     * Flag to check if message has been initialised
     * @var bool
     */
    protected $_is_message_set = false;

    /**
     * Message types with their associated delimeters
     * @var array
     */
    protected $_message_types = array(
        'ERROR' => array(
            'prefix' => '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>',
            'suffix' => '</div>'
            ),
        'SUCCESS' => array(
            'prefix' => '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>',
            'suffix' => '</div>'
            ),
        'NOTICE' => array(
            'prefix' => '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>',
            'suffix' => '</div>'
            ),
        'WARNING' => array(
            'prefix' => '<div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button>',
            'suffix' => '</div>'
            ),
        'MESSAGE' => array(
            'prefix' => '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>',
            'suffix' => '</div>'),
        'SYSTEM' => array(
            'prefix' => '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>',
            'suffix' => '</div>'
            )
            );

            /**
             * Create a new Message
             *
             * @param string $message_type message type constant (Vanilla_Message::Error)
             * @param string $message      message
             * 
             * @chainable
             * 
             * @return Message
             */
            public function __construct($message_type, $message)
            {
                return $this->create($message_type, $message);
            }

            /**
             * Creates a new Message instance
             *
             * @param string $message_type message type constant (Vanilla_Message::Error)
             * @param string $message      message
             * 
             * @chainable
             * 
             * @return Message
             */
            public static function factory($message_type, $message)
            {
                return new self($message_type, $message);
            }

            /**
             * Create a new message
             *
             * @param string $message_type message type constant (Vanilla_Message::Error)
             * @param string $message      message
             * 
             * @chainable
             * 
             * @return Message
             */
            protected function create($message_type, $message)
            {
                $purifier = new Vanilla_Purifier();
                $message  = $purifier->purify($message);
                
                $success = true;

                // message is null; return $this
                if ($message === NULL) {
                    return $this;
                }

                // message type range check
                if (!array_key_exists($message_type, $this->_message_types)) {
                    trigger_error("Unknown message type", E_USER_WARNING);
                     
                    $message_type = self::SUCCESS;
                }

                // string check
                if (!is_string($message)) {
                    trigger_error("Message has not been set; Message must be a string", E_USER_WARNING);
                     
                    $success = false;
                }

                // empty check
                if (empty($message)) {
                    trigger_error("Message has not been set; Message is empty", E_USER_WARNING);
                     
                    $success = false;
                }

                if ($success) {
                    // set the message
                    $this->message = $message;
                     
                    // set the message type
                    $this->message_type = $message_type;
                     
                    // message has been set
                    $this->_is_message_set = true;
                }

                return $this;
            }

            /**
             * Get the raw message or include the delimeters
             *
             * @param bool $include_delimeters [optional]
             * 
             * @chainable
             * 
             * @return Message
             */
            public function fetch($include_delimeters = true)
            {

                // type check
                if (!is_bool($include_delimeters)) {
                    trigger_error("Boolean expected", E_USER_WARNING);
                     
                    $include_delimeters = true;
                }

                // build message
                if ($include_delimeters) {

                    $message = $this->_message_types[$this->message_type]['prefix'] 
                        . $this->message 
                        . $this->_message_types[$this->message_type]['suffix'];
                } else {
                    $message = $this->message;
                }

                return $message;
            }

            /**
             * Convert message object to string
             *
             * @return string
             */
            public function __toString()
            {
                $str = '';

                if ($this->_is_message_set) {
                    $str = $this->fetch();
                }

                return $str;
            }

            /**
             * Save this message to the session
             * 
             * @return void
             */
            public function saveToSession()
            {
                if (!empty($_SESSION['vanilla_messages'])) {
                    $_SESSION['vanilla_messages'][] = serialize($this);
                } else {
                    $_SESSION['vanilla_messages'] = array(serialize($this));
                }
            }

} // End Message