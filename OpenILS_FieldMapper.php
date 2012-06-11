<?php
/**
    @file
    @brief A Fieldmapper for OpenILS/OpenSRF
    @uses '/openils/conf/fm_IDL.xml'

    @author David Busby
    @copyright edoceo, inc.
    @license BSD

*/

/**
    @brief the OpenILS Field Mapper
*/
class OpenILS_FieldMapper
{
    protected $_idl = '/openils/conf/fm_IDL.xml';
    protected $_map_a; // ID to Name
    protected $_map_b; // Name to ID
    protected $_obj;
    protected $_xml
    /**
        @param $obj object to map
        @param $idl optional IDL file
    */
    function __construct($obj,$idl=null)
    {
        $this->_obj = $obj;
        if (!empty($obj->__p)) {
            $this->_obj = $obj->__p;
        }
        if (!empty($obj->__c)) {
            $this->setMap($obj->__c);
        }
        if ( (!empty($idl)) && (is_file($idl)) ) {
            $this->_idl = $idl;
        }
    }
    /**
        Set the Map based on the Object Class
        @param name the name to map to
        @return void
    */
    function setMap($name)
    {
        if (empty($this->_xml)) {
            $this->_xml = simplexml_load_file($this->_idl);
        }
        // // $res = $xml->getDocNamespaces(true);
        // // @todo should xpath query
        foreach ($xml->class as $c) {
            // echo "if ({$c['id']} == $name) {<br>";
            if ($c['id'] == $name) {
                $i = 0;
                foreach ($c->fields->field as $f) {
                    // radix::dump($f);
                    $n = strval($f['name']);
                    $this->_map_a[ $i ] = $n;
                    $this->_map_b[ $n ] = $i;
                    $i++;
                }
                // radix::dump($this->_map_a);
                // radix::dump($this->_map_b);
                return(0);
            }
        }
    }

    /**
        Convert to An Array K=>V
        @return array
    */
    function toArray()
    {
        $ret = array();
        foreach ($this->_map_b as $k=>$v) {
            $ret[$k] = $this->_obj[$v];
        }
        return $ret;
    }
    /**
        Magic Getter
        @return object property
    */
    function __get($k)
    {
        return $this->_obj[ $this->_map_b[ $k ] ];
    }
}