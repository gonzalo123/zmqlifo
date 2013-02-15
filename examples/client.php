<?php
include __DIR__ . '/../vendor/autoload.php';

use Zmqlifo\Client;

$queue = Client::factory('tcp://127.0.0.1:4444');
echo $queue->run("ls -latr")->getOutput();
echo $queue->run("pwd")->getOutput();