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

$publisher = new \SimpleQueue\Messaging\JsonQueuePublisher('test', $channel, new \Psr\Log\NullLogger());

$instance = new MyObject(uniqid());

if ($publisher->publish($instance)) {
    echo "published test event";
} else {
    echo "failed to publish event";
}

