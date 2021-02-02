<?php
declare(strict_types=1);


namespace SimpleQueue\Messaging;


use Psr\Log\LoggerInterface;

class AmqpObjectFactory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function hydrate(array $object): ?object {
        $this->logger->info('Received object', $object);

        /** @var $class AbstractAmqpObject */
        $class = $object['class_name'];

        try {
            return $class::createFromArray($object);
        } catch (\Throwable $exception) {
            $this->logger->error('Could not reconstruct object', $object);
            return null;
        }
    }
}