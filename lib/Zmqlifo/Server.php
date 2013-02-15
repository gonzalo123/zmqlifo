<?php

namespace Zmqlifo;

use React\EventLoop;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;

class Server
{
    private $socket;
    private $loop;
    private $socketDealer;
    private $callback;

    public function __construct(EventLoop\LoopInterface $loop, SocketWrapper $socketDealer)
    {
        $this->loop         = $loop;
        $this->socketDealer = $socketDealer;
    }

    public static function factory($socket)
    {
        $loop         = EventLoop\Factory::create();
        $context      = new Context($loop);
        $socketDealer = $context->getSocket(\ZMQ::SOCKET_DEALER);

        $queueServer = new Server($loop, $socketDealer);
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
        $this->handleSockets();
        $this->loop->run();
    }

    public function handleSockets()
    {
        $this->socketDealer->on('message', function ($msg) {
            $this->socketDealer->send($this->invokeCallback($msg));
        });

        $this->socketDealer->bind($this->socket);
    }

    public function invokeCallback($msg)
    {
        return call_user_func($this->callback, $msg);
    }


}