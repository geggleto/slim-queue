<?php
declare(strict_types=1);

namespace SimpleQueue\Messaging;

interface QueuePublisherInterface
{
    public function publish(AbstractAmqpObject $object): bool;
}