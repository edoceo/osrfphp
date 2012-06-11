<?php
/**
    @file
    @brief A Message from the OpenSRF Wire

    @author David Busby
    @copyright edoceo, inc.
    @license BSD

*/

class osrfMessage
{
    protected $_method;

    function __construct($x)
    {
        if (is_string($x)) {
            $this->_method = $x;
        } elseif (is_object($x)) {
            $this->_method = $x;
            // radix::dump($x);
        }
    }
    /**
    
    */
    function getMethod()
    {
        return $this->_method;
    }
    /**
    */
    function toArray()
    {
        $ret = array(
            '__c' => 'osrfMessage',
            '__p' => array(
                'threadTrace' => 0,
                'locale' => 'en-US',
                'type' => 'REQUEST',
                'payload' => $this->_method->toArray()
            ),
        );
        return $ret;
    }
    /**
    */
    function asJSON()
    {
        $ret = $this->toArray();
        return json_encode($ret);
    }
}
