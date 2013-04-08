<?php

namespace Zmqlifo;

class Client
{
    private $socket;
    private $socketDealer;
    private $output;

    public function __construct(\ZMQSocket $socketDealer)
    {
        $this->socketDealer = $socketDealer;
    }

    public function setSocket($socket)
    {
        $this->socket = $socket;
    }

    public function run($command)
    {
        $this->socketDealer->connect($this->socket);
        $this->socketDealer->send($command);

        return $this;
    }

    public function getOutput()
    {
        return $this->socketDealer->recv();
    }

    static function factory($socket)
    {
        $context = new \ZMQContext();
        $dealer  = $context->getSocket(\ZMQ::SOCKET_DEALER);

        $queue = new Client($dealer);
        $queue->setSocket($socket);

        return $queue;
    }
}

