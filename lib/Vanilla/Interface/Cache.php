<?php

/**
 * Vanilla_Interface_Cache
 *
 * @name       Vanilla_Interface_Cache
 * @category   Interface
 * @package    Vanilla
 * @subpackage Interface
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.3
 * @link       http://192.168.50.14/vanilla-doc/
 */

/**
 * Vanilla_Interface_Cache
 *
 * @name       Vanilla_Interface_Cache
 * @category   Interface
 * @package    Vanilla
 * @subpackage Interface
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.3
 * @link       http://192.168.50.14/vanilla-doc/
 */


interface Vanilla_Interface_Cache
{

    public function get($key);

    public function set($key, $value);

}