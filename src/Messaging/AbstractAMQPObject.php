<?php
declare(strict_types=1);


namespace SimpleQueue\Messaging;


abstract class AbstractAMQPObject implements AMQPObjectInterface
{
    abstract public static function createFromArray(array $payload);

    abstract public function getRoutingKey(): string;

    abstract public function correlationId(): ?string;

    abstract public function appilicationId(): ?string;
}