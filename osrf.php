<?php
/**
    An Interface to OpenSRF HTTP Translator
    It will get an put stuff to the HTTP translator
*/

/**
    osrf class sends and recieves messages, does some field mapping too (maybe?)
*/

class osrf
{
    const UA = 'Edoceo OpenSRF PHP Library 2012.13';

    private static $_conf_uri;
    private static $_conf_idl = '/openils/conf/fm_IDL.xml';

    protected $_host_uri;  // Base of Evergreen Server 

    /**
    */
    function __construct($opt)
    {
        if (is_string($opt)) {
            $this->_uri_base = $opt;
        } elseif (is_array($opt)) {
            $this->_uri_base = $opt['host'];
            $this->_idl_file = $opt['idl_file'];
        };
    }
    static function easy($api,$arg)
    {
        $req = new osrfMethod($api); // Booking Resource Type
        //$obj->addParam('6d5624a252a75dcd802e779a7187b2be');
        //$obj->addParam($brsrc->type);
        foreach ($arg as $a) $req->addParam($a);
        $msg = new osrfMessage($req);
        // $msg = new osrfMessage($req);
        // $r = $c->send($msg);
        $c = new osrf(self::$_conf_uri);
        if ($ret = $c->send($msg)) {
            if (count($ret)==2) {
                if ( ('osrfMessage' == $ret[1]->__c)
                    && ('STATUS' == $ret[1]->__p->type)
                    && (205 == $ret[1]->__p->payload->__p->statusCode) ) {
                    // $ret = $res[0]->__p->payload->__p->content);
                    $ret = self::map($ret[0]->__p->payload->__p->content);
                }
            }
        }
        // $ret = new OpenILS_FieldMapper($res[0]->__p->payload->__p->content);
        // radix::dump($brt->toArray());
        return $ret;

    }
    /**
        get/set the default uri
    */
    static function idl($f=null)
    {
        if ($f) self::$_conf_idl = $f;
        return self::$_conf_idl;
    }
    static function uri($h=null)
    {
        if ($h) self::$_conf_uri = $h;
        return self::$_conf_uri;
    }
    /**
        @param $msg osrfMessage
    */
    function send($msg=null)
    {
        if (empty($msg)) {
            return false;
        }
        $post = 'osrf-msg=' . json_encode(array($msg->toArray()));

        $ch = $this->_curl_init($this->_uri_base);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post);

        $head = array(
            sprintf('X-OpenSRF-service: %s',$msg->getMethod()->getService()),
            // 'X-OpenSRF-xid: A',
            // 'X-OpenSRF-thread: 1192540427.567678.119254042731367',
            'X-OpenSRF-multipart: false', // Don't give a multi-part response
            'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);

        $buf = curl_exec($ch);
        $inf = curl_getinfo($ch);
        // radix::dump($inf);
        // radix::dump($this->_message->asJSON());
        // print_r($res);

        $ret = self::_parse_response($buf);

        return $ret;
    }
    /**
        
    */
    // function setMessage($msg)
    // {
    //     $this->_message = $msg;
    // }
    private static function _parse_response($buf)
    {
        if (substr($buf,0,2) != '[{') {
            return false;
        }

        $res = json_decode($buf);
        // if (is_array($res)) {
        //     foreach ($res as $obj) {
        //         $cls = $obj->__c;
        //         $obj = new $cls($obj);
        //     }
        // }

        return $res;
    }
    /**
        Initialise my Curl Handle
        @return curl handle
    */
    private static function _curl_init($uri)
    {
        $ch = curl_init($uri);
        // Booleans
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, false);
        curl_setopt($ch, CURLOPT_CRLF, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NETRC, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        // if ( (!empty(self::$_opts['verbose'])) && (is_resource(self::$_opts['verbose'])) ) {
        //     curl_setopt(self::$_ch, CURLOPT_VERBOSE, true);
        //     curl_setopt(self::$_ch, CURLOPT_STDERR, self::$_opts['verbose']);
        // }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_USERAGENT,self::UA);
        // if ( (!empty(self::$_opts['head'])) ) {
        //     curl_setopt(self::$_ch, CURLOPT_HTTPHEADER, self::$_opts['head']);
        // }
        // curl_setopt($ch,CURLOPT_POSTFIELDS,array('osrf-msg'=>json_encode($data)));
        return $ch;
    }
    /**
        Field Mapper Assist?
    */
    static function _idl2php()
    {
        // @todo load the file
        // @todo serialize for php?
    }
    /**
    */
    static function map($o)
    {
        $xml = simplexml_load_file(self::$_conf_idl);

        // @todo should xpath query
        // $dns = $xml->getDocNamespaces(true);
        // // radix::dump($dns);
        // foreach ($dns as $p=>$u) {
        //     // radix::dump($x);
        //     $xml->registerXPathNamespace($p,$u);
        // }
        // $map = $xml->xpath('//class'); // [id=' . $o->__c . ']');
        // radix::dump($map);

        // Brute Force :(
        foreach ($xml->class as $c) {
            if ($c['id'] != $o->__c) {
                continue;
            }

            $ret = new stdClass();
            $idx = 0;
            foreach ($c->fields->field as $f) {
                $n = strval($f['name']);
                $ret->$n = isset($o->__p[$idx]) ? $o->__p[$idx] : null;
                $idx++;
            }
            return $ret;
        }

        return false;
    }
}
