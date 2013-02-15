<?php

use Zmqlifo\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleClient()
    {
        $loop = $this->getMock('React\EventLoop\LoopInterface');
        /** @var React\ZMQ\SocketWrapper $socketDealer */
        $socketDealer = $this->getMockBuilder('React\ZMQ\SocketWrapper')->disableOriginalConstructor()->getMock();

        $queue = new Client($loop, $socketDealer);

        $socketDealer->expects($this->any())->method('emit')->will($this->returnCallback(function ($event, $arguments) use ($queue) {
            $class  = new \ReflectionClass('Zmqlifo\\Client');
            $property = $class->getProperty('output');
            $property->setAccessible(true);
            $property->setValue($queue, $arguments['message']);
        }));

        $queue->setSocket('tcp://127.0.0.1:4444');
        $queue->run("command");
        $socketDealer->emit('message', array('message' => 'command'));

        $this->assertEquals('command', $queue->getOutput());
    }
}