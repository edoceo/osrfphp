<?php
/**
    @file
    @brief OpenSRF Method Class

    @author David Busby
    @copyright edoceo, inc.
    @license BSD

    @example examples/auth.php

*/

class osrfMethod
{
    protected $_locale;
    protected $_method;
    protected $_params;
    protected $_service;

    function __construct()
    {
        switch (func_num_args()) {
        case 0:
        case 1:
            $this->_method = func_get_arg(0);
            break;
        case 2:
            $this->_method = func_get_arg(0);
            $this->_locale = func_get_arg(1);
            break;
        }
        if (preg_match('/^([\w\-]+\.[\w\-]+)\./',$this->_method,$m)) {
            $this->_service = $m[1];
        }
    }
    function addParam($v)
    {
        $this->_params[] = $v;
    }
    function setParam($i,$v)
    {
        $this->_params[$i] = $v;
    }
    /**
        @return string Service which is set on Construction
    */
    function getService()
    {
        return $this->_service;
    }
    /**
    */
    function toArray()
    {
        $ret = array(
            '__c' => 'osrfMethod',
            '__p' => array(
                'method' => $this->_method,
                'params' => $this->_params,
            ),
        );
        return $ret;
    }
    /**
    */
    function asJSON()
    {
        // make the Array
    }
}

