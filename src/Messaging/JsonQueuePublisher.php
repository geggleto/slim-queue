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

    public function publish(AbstractAMQPObject $object): bool
    {
        $wrapper = [
            'classFQN' => get_class($object),
            'correlation_id' => $object->correlationId(),
            'application_id' => $object->appilicationId(),
            'data' => $object->serialize()
        ];
        $message = json_encode($wrapper);

        $options = $this->options + [
            'app_id' => $wrapper['application_id'],
            'correlation_id' => $wrapper['correlation_id']
        ];

        $amqpmessage = new AMQPMessage($message, $options);

        try {
            $this->channel->basic_publish($amqpmessage, $this->exchangeName, $object->getRoutingKey());

            return true;
        } catch (\Exception $exception) {
            $this->logger->error('Failed to publish object', [$wrapper, $exception->getMessage(), $exception->getTrace()]);
            return false;
        }
    }
}