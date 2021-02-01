<?php
declare(strict_types=1);

use SimpleQueue\Messaging\AbstractAMQPObject;

include_once __DIR__ . '/../vendor/autoload.php';

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

class MyHandler implements \SimpleQueue\Messaging\MessageHandler {

    public function handle(AbstractAMQPObject $object)
    {
        var_dump($object->serialize());
    }
}