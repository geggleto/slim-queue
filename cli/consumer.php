<?php
declare(strict_types=1);

include_once __DIR__ . '/shared.php';

$config = new \SimpleQueue\Config\AmqpConfigFactory(
    'localhost',
    '5672',
    'guest',
    'guest'
);

$channel = $config->getChannelAndDeclare('/', 'test', MyObject::getRoutingKey());

$nilLogger = new \Psr\Log\NullLogger();

$consumer = new \SimpleQueue\Messaging\JsonQueueConsumer(
    MyObject::getRoutingKey(),
    $channel,
    'consumer-'.uniqid(),
    new \SimpleQueue\Messaging\AmqpObjectFactory($nilLogger),
    new MyHandler(),
    $nilLogger
);

$consumer->consume();