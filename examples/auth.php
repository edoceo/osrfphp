<?php
/**
    Authenticate to the Evergreen ILS System
    
    @see http://open-ils.org/~denials/workshop.html#_authentication
*/

$host = 'http://ils-server/osrf-http-translator';
$user = 'openils';
$pass = 'openils';

// Connection
$oht = new OpenSRF_HTTP($host);

// New Method
$obj = new OpenSRF_Method('open-ils.auth.authenticate.init');
$obj->addParam($user);
$msg = new osrfMessage($obj);
$res = $oht->send($msg);
radix::dump($res);

$seed = $res[0]->__p->payload->__p->content;

// Now Do the Authentincate
$obj = new OpenSRF_Method('open-ils.auth.authenticate.complete');
$obj->addParam(array(
    'username' => $user,
    'password' => md5($seed . md5($pass)),
));

$msg = new osrfMessage($obj);
$res = $oht->send($msg);
radix::dump($res);