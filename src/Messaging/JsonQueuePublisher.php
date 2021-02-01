<?php


namespace SimpleQueue\Messaging;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class JsonQueuePublisher implements QueuePublisherInterface
{
    private AMQPChannel $channel;
    private string  $exchangeName;
    private array $options;
    private LoggerInterface $logger;

    public function __construct($exchangeName, AMQPChannel $channel, LoggerInterface $logger)
    {
        $this->channel = $channel;
        $this->exchangeName = $exchangeName;
        $this->options = ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
        $this->logger = $logger;
    }

    public function publish(AbstractAmqpObject $object): bool
    {
        $message = json_encode($object->jsonSerialize());

        $options = $this->options + [
            'app_id' => $object->getApplicationId(),
            'correlation_id' => $object->getCorrelationId()
        ];

        $message = new AMQPMessage($message, $options);

        try {
            $this->channel->basic_publish($message, $this->exchangeName, $object->getRoutingKey());

            return true;
        } catch (\Exception $exception) {
            $this->logger->error('Failed to publish object', [$object->jsonSerialize(), $exception->getMessage(), $exception->getTrace()]);
            return false;
        }
    }
}