<?php

/**
 * Vanilla Exception Route
 *
 * @name       Vanilla Exception Route
 * @category   Exception
 * @package    Vanilla
 * @subpackage Exception
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla Exception Route
 *
 * @name       Vanilla Exception Route
 * @category   Exception
 * @package    Vanilla
 * @subpackage Exception
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @link       http://192.168.50.14/vanilla-doc/
 */

class Vanilla_Exception_Route extends Vanilla_Exception
{

    /**
     * Vanilla_Exception_Route specific function that will return the
     * correct Error Controller Action based on the Code passed when throwing exception
     * this will be used in Router
     *
     * @example 404 code will return error404Action
     *
     * @return string
     */

    public function getAction()
    {
        return "error".$this->getCode()."Action";
    }


}