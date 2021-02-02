<?php
declare(strict_types=1);

include_once __DIR__ . '/../vendor/autoload.php';

class MyObject extends \SimpleQueue\Messaging\AbstractAmqpObject {
    protected const ROUTING_KEY = 'MyQueue.Cool';

    public function getApplicationId(): ?string
    {
        return 'sf-internal-web';
    }
}

class MyHandler implements \SimpleQueue\Messaging\MessageHandler {

    public function handle(\SimpleQueue\Messaging\AmqpObjectInterface $object)
    {
        var_dump($object->getPayload());
    }
}