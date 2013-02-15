<?php
include __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Process\Process;
use Zmqlifo\Server;

$server = Server::factory('tcp://127.0.0.1:4444');
$server->registerOnMessageCallback(function ($msg) {
    $process = new Process($msg);
    $process->setTimeout(3600);
    $process->run();
    return $process->getOutput();
});

$server->run();
