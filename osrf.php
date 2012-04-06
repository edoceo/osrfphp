<?php
/**
    An Interface to OpenSRF HTTP Translator
    It will get an put stuff to the HTTP translator
*/

class osrf
{
    const UA = 'Edoceo OpenSRF PHP Library 2012.13';

    protected $_uri_base;  // Base of Evergreen Server 
    protected $_idl_file = '/openils/conf/fm_IDL.xml';
    /**
    */
    function __construct()
    {
        switch (func_num_args()) {
        case 1:
            $this->_uri_base = func_get_arg(0);
            break;
        }
    }

    // __c is the class
    // __p is the props
    // @param $msg is an OpenSRF_Message
    function send($msg=null)
    {
        if (empty($msg)) {
            return false;
        }
        $post = 'osrf-msg=' . json_encode(array($msg->toArray()));

        $ch = $this->_curl_init($this->_uri_base);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post);

        $head = array(
            sprintf('X-OpenSRF-service: %s',$msg->getMethod()->getService()), // Has to Match the Request Method
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
}
