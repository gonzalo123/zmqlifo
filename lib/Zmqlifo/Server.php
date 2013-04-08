<?php

namespace Zmqlifo;

class Server
{
    private $socket;
    private $socketDealer;
    private $callback;

    public function __construct(\ZMQSocket $socketDealer)
    {
        $this->socketDealer = $socketDealer;
    }

    public static function factory($socket)
    {
        $context      = new \ZMQContext();
        $socketDealer = $context->getSocket(\ZMQ::SOCKET_DEALER);

        $queueServer = new Server($socketDealer);
        $queueServer->setSocket($socket);

        return $queueServer;
    }

    public function setSocket($socket)
    {
        $this->socket = $socket;
    }

    public function registerOnMessageCallback($callback)
    {
        $this->callback = $callback;
    }

    public function run()
    {
        $this->socketDealer->bind($this->socket);

        while (true) {
            $this->tick();
        }
    }

    public function tick()
    {
        $msg = $this->socketDealer->recv();
        $result = $this->invokeCallback($msg);
        $this->socketDealer->send($result);
    }

    public function invokeCallback($msg)
    {
        return call_user_func($this->callback, $msg);
    }


}