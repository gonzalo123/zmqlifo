<?php

use Zmqlifo\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleClient()
    {
        /** @var ZMQSocket $socketDealer */
        $socketDealer = $this->getMockBuilder('ZMQSocket')->disableOriginalConstructor()->getMock();

        $queue = new Client($socketDealer);

        $socketDealer->expects($this->any())->method('recv')->will($this->returnValue('command'));

        $queue->setSocket('tcp://127.0.0.1:4444');
        $queue->run("command");

        $this->assertEquals('command', $queue->getOutput());
    }
}