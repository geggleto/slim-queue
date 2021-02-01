<?php
declare(strict_types=1);

include_once __DIR__ . '/../vendor/autoload.php';

$config = new \SimpleQueue\Config\AmqpConfigFactory(
    'localhost',
    '5672',
    'guest',
    'guest'
);

$channel = $config->getChannelAndDeclare('/', 'test', 'MyQueue.Cool');

$publisher = new \SimpleQueue\Messaging\JsonQueuePublisher('test', $channel, new \Psr\Log\NullLogger());

class MyObject extends \SimpleQueue\Messaging\AbstractAMQPObject {

    private string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }

    public function serialize(): array
    {
        return [
            'value' => $this->value
        ];
    }

    public static function createFromArray(array $payload)
    {
        return new self($payload['value'] ?? '');
    }

    public function getRoutingKey(): string
    {
        return 'MyQueue.Cool';
    }

    public function correlationId(): ?string
    {
        return uniqid();
    }

    public function appilicationId(): ?string
    {
        return 'sf-internal';
    }
}

$instance = new MyObject(uniqid());

if ($publisher->publish($instance)) {
    echo "published test event";
} else {
    echo "failed to publish event";
}

