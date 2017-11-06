<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use SlimQ\Messaging\QueuePublisher;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/SampleAddTask.php';

$stream = new AMQPStreamConnection(
    '127.0.0.1',
    '5672',
    'guest',
    'guest',
    '/'
);

$channel = $stream->channel();

$publisher = new QueuePublisher(
    'my_exchange', $channel
);

$publisher->publish('my_queue', SampleAddTask::class, [1,1]);

echo 'Sent Message\n';