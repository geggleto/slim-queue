<?php


namespace SimpleQueue\Messaging;


interface AmqpObjectInterface extends \JsonSerializable
{
    public static function createFromArray(array $payload);

    public static function getRoutingKey(): string;

    public function getCorrelationId(): ?string;

    public function getApplicationId(): ?string;

    public function getPayload(): array;
}