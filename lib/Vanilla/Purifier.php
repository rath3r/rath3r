<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "ext/htmlpurifier-4.5.0/library/HTMLPurifier.auto.php";

class Vanilla_Purifier
{
    private $_purifier;
    
    public function __construct($config = null)
    {
        if($config === null)
        {
            $config = HTMLPurifier_Config::createDefault();
        }
        $config->set('Cache.SerializerPath', BASE_DIR . '../cache/');
        $config->set('CSS.ForbiddenProperties', array('font-size', 'line-height'));
        $this->_purifier = new HTMLPurifier($config);
    }
    
    public function purify($value)
    {
        return $this->_purifier->purify($value);
    }
    
}

