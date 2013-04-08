<?php

use Zmqlifo\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleClient()
    {
        $server = Server::factory('tcp://127.0.0.1:4444');

        /** @var ZMQSocket $socketDealer */
        $socketDealer = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();

        $server = new Server($socketDealer);

        $flag = 0;
        $server->registerOnMessageCallback(function ($msg) use (&$flag) {
            $flag++;
        });

        $socketDealer->expects($this->any())->method('recv')->will($this->returnValue('hi'));

        $this->assertEquals(0, $flag);

        $server->tick();

        $this->assertEquals(1, $flag);
    }
}