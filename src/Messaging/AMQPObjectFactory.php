<?php
declare(strict_types=1);


namespace SimpleQueue\Messaging;


use Psr\Log\LoggerInterface;

class AMQPObjectFactory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {

        $this->logger = $logger;
    }

    public function hydrate(array $object): ?AbstractAMQPObject {
        $this->logger->info('Received object', $object);

        /** @var $class AbstractAMQPObject */
        $class = $object['classFQN'];

        try {
            return $class::createFromArray($object['data']);
        } catch (\Throwable $exception) {
            $this->logger->error('Could not reconstruct object', $object);
            return null;
        }
    }
}