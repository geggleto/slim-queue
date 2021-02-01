<?php
declare(strict_types=1);

include_once __DIR__ . '/shared.php';

$config = new \SimpleQueue\Config\AmqpConfigFactory(
    'localhost',
    '5672',
    'guest',
    'guest'
);

$instance = new MyObject([
    'myPayload' => uniqid()
]);

$channel = $config->getChannelAndDeclare('/', 'test', MyObject::getRoutingKey());

$publisher = new \SimpleQueue\Messaging\JsonQueuePublisher('test', $channel, new \Psr\Log\NullLogger());

if ($publisher->publish($instance)) {
    echo "published test event";
} else {
    echo "failed to publish event";
}

