<?php
declare(strict_types=1);

include_once __DIR__ . '/shared.php';

$config = new \SimpleQueue\Config\AmqpConfigFactory(
    'localhost',
    '5672',
    'guest',
    'guest'
);

$channel = $config->getChannelAndDeclare('/', 'test', 'MyQueue.Cool');

$nilLogger = new \Psr\Log\NullLogger();

$consumer = new \SimpleQueue\Messaging\JsonQueueConsumer(
    'MyQueue.Cool',
    $channel,
    'consumer-'.uniqid(),
    new \SimpleQueue\Messaging\AMQPObjectFactory($nilLogger),
    new MyHandler(),
    $nilLogger
);

$consumer->consume();