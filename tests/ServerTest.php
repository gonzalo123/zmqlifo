<?php

use Zmqlifo\Server;
use React\EventLoop;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleClient()
    {
        $server = Server::factory('tcp://127.0.0.1:4444');

        $loop = EventLoop\Factory::create();

        /** @var React\ZMQ\SocketWrapper $socketDealer */
        $socketDealer = $this->getMockBuilder('React\ZMQ\SocketWrapper')->disableOriginalConstructor()->getMock();

        $server = new Server($loop, $socketDealer);

        $flag = 0;
        $server->registerOnMessageCallback(function ($msg) use (&$flag) {
            $flag++;
        });

        $socketDealer->expects($this->any())->method('emit')->will($this->returnCallback(function ($event, $arguments) use ($server) {
            $class  = new \ReflectionClass('Zmqlifo\\Server');
            $method = $class->getMethod('invokeCallback');
            $method->setAccessible(true);
            $method->invokeArgs($server, [$arguments]);
        }));

        $this->assertEquals(0, $flag);

        $socketDealer->emit('message', ['message' => 'hi']);

        $this->assertEquals(1, $flag);
        $loop->tick();
    }
}