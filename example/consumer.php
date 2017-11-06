<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use SlimQ\Messaging\QueueConsumer;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/SampleAddTask.php';
include_once __DIR__ . '/SampleContainer.php';

$container = new SampleContainer();
$container->put(SampleAddTask::class, new SampleAddTask());

$stream = new AMQPStreamConnection(
    '127.0.0.1',
    '5672',
    'guest',
    'guest',
    '/'
);

$channel = $stream->channel();

$consumer = new QueueConsumer(
    'my_queue',
    $channel,
    $container
);

$consumer->consume();