[![Build Status](https://travis-ci.org/gonzalo123/zmqlifo.png?branch=master)](https://travis-ci.org/gonzalo123/zmqlifo)

ZeroMQ LIFO Queue

usage examples:

Client:
```php
<?php
include __DIR__ . '/../vendor/autoload.php';

use Zmqlifo\Client;

$queue = Client::factory('tcp://127.0.0.1:4444');
echo $queue->run("ls -latr")->getOutput();
echo $queue->run("pwd")->getOutput();
```

Server
```php
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
```

