<?php

namespace Airavata\UserAPI;

$GLOBALS['THRIFT_ROOT'] = 'Thrift/';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TTransport.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TTransportException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TMessageType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Factory/TStringFuncFactory.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/TStringFunc.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/Core.php';

$GLOBALS['AIRAVATA_ROOT'] = 'Airavata/';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'UserAPI/UserAPI.php';

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Airavata\UserAPI\UserAPIClient;

class UserAPIClientFactory
{

    private $userapiServerHost;
    private $userapiServerPort;
    private $thriftTimeout;

    public function __construct($options)
    {
        $this->userapiServerHost = isset($options['userapiServerHost']) ? $options['userapiServerHost'] : "localhost";
        $this->userapiServerPort = isset($options['userapiServerPort']) ? $options['userapiServerPort'] : "7430";
        $this->timeout = isset($options['thriftTimeout']) ? intval($options['thriftTimeout']) : 5000;
    }

    public function getUserAPIClient()
    {
        $transport = new TSocket($this->userapiServerHost, $this->userapiServerPort);
        $transport->setRecvTimeout(5000);
        $transport->setSendTimeout(5000);
        $protocol = new TBinaryProtocol($transport);
        $transport->open();
        return new UserAPIClient($protocol);
    }
}