<?php
/**

*/

class OpenILS_FieldMapper
{
    protected $_map_a; // ID to Name
    protected $_map_b; // Name to ID
    protected $_obj;

    protected static $_idl = '/openils/conf/fm_IDL.xml';

    /**
    */
    function __construct($obj)
    {
        $this->_obj = $obj;
        if (!empty($obj->__p)) {
            $this->_obj = $obj->__p;
        }
        if (!empty($obj->__c)) {
            $this->setMap($obj->__c);
        }
    }
    /**
    */
    static function setIDL($f)
    {
        self::$_idl = $f;
    }
    /**
    */
    function setMap($name)
    {
        // error_reporting(0xffffffff);
        // $doc = new DOMDocument();
        // $doc->validateOnParse = true;
        // radix::dump($doc->load(Radix::$root . '/etc/fm_IDL.xml'));
        // // $doc->validate();
        // 
        // $map = $doc->getElementById('brt');
        // radix::dump($map);

        $xml = simplexml_load_file(self::$_idl);
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
    */
    function toArray()
    {
        $ret = array();
        foreach ($this->_map_b as $k=>$v) {
            $ret[$k] = @$this->_obj[$v];
        }
        return $ret;
    }
    /**
    */
    function __get($k)
    {
        return $this->_obj[ $this->_map_b[ $k ] ];
    }
}