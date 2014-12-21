<?php

/**
 * Recaptcha - uses the Google recaptchalib.php class
 *
 * @name     Vanilla Recaptcha
 * @category Recaptcha
 * @package  Vanilla
 * @author   Niall St John <niall.stjohn@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

require_once LIB_FRAMEWORK_DIR . 'Vanilla/ext/recaptchalib.php';

/**
 * Recaptcha - uses the Google recaptchalib.php class
 *
 * @name     Vanilla Recaptcha
 * @category Recaptcha
 * @package  Vanilla
 * @author   Niall St John <niall.stjohn@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Recaptcha
{

    /**
     * Public key
     * @var string
     */
    public $publickey;

    /**
     * Private key
     * @var string
     */
    public $privatekey;

    /**
     * Form html
     * @var string
     */
    public $form_html;


    /**
     * Create a new Message
     *
     * @param string $publickey  Public Recaptcha Key
     * @param string $privatekey Private Recaptcha Key
     *
     * @return Vanilla_Recaptcha
     */
    public function __construct($publickey,$privatekey)
    {

        $this->publickey = $publickey;
        $this->privatekey = $privatekey;
        $this->form_html = recaptcha_get_html($this->publickey);
    }

    /**
     * Check for errors
     *
     * @param array $errors Errors 
     *
     * @return array
     */
    public function check($errors = array())
    {

        $resp = recaptcha_check_answer(
            $this->privatekey,
            $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]
        );

        if (!$resp->is_valid) {
            if (!is_array($errors)) {
                $errors = array();
            }
            $errors[] = 'Complete the verfication.';
        }

        return $errors;

    }
    
    public function __toString()
    {
        return $this->form_html;
    }
}