<?php

namespace Zmqlifo;

use React\EventLoop;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;

class Client
{
    private $socket;
    private $loop;
    private $socketDealer;
    private $output;

    public function __construct(EventLoop\LoopInterface $loop, SocketWrapper $socketDealer)
    {
        $this->loop         = $loop;
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
        $this->socketDealer->on('message', function ($msg) {
            $this->output = $msg;
            $this->loop->stop();
        });
        $this->loop->run();

        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }

    static function factory($socket)
    {
        $loop    = EventLoop\Factory::create();
        $context = new Context($loop);
        $dealer  = $context->getSocket(\ZMQ::SOCKET_DEALER);

        $queue = new Client($loop, $dealer);
        $queue->setSocket($socket);

        return $queue;
    }
}
 
