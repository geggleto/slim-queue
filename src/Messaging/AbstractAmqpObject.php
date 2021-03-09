<?php
declare(strict_types=1);


namespace SimpleQueue\Messaging;


abstract class AbstractAmqpObject implements AmqpObjectInterface
{
    protected static string $routingKey;
    private array $payload;
    protected string $correlationId;

    public function __construct(array $payload, ?string $correlationId = null) {
        $this->payload = $payload;
        $this->correlationId = $correlationId ?? uniqid();
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public static function getRoutingKey(): string
    {
        return static::$routingKey;
    }

    public static function createFromArray(array $data)
    {
        return new static($data['payload'] ?? [], $data['correlation_id'] ?? null);
    }

    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    public function jsonSerialize(): array
    {
        return [
            'class_name' => static::class,
            'correlation_id' => $this->getCorrelationId() ?? '',
            'application_id' => $this->getApplicationId() ?? '',
            'payload' => $this->getPayload(),
        ];
    }
}