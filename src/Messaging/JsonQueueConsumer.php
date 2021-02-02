<?php


namespace SimpleQueue\Messaging;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class JsonQueueConsumer
{
    private AMQPChannel $channel;
    private string $queueName;
    private string $identifier;
    private AmqpObjectFactory $AMQPObjectFactory;

    private MessageHandler $handler;

    private LoggerInterface $logger;

    public function __construct(string $queueName, AMQPChannel $channel, string $identifier, AmqpObjectFactory $AMQPObjectFactory, MessageHandler $handler, LoggerInterface  $logger)
    {
        $this->channel = $channel;
        $this->queueName = $queueName;
        $this->identifier = $identifier;
        $this->AMQPObjectFactory = $AMQPObjectFactory;
        $this->handler = $handler;
        $this->logger = $logger;
    }

    public function consume()
    {

        $this->channel->basic_consume($this->queueName, $this->identifier, false, false, false, false, [$this, 'start']);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function start(AMQPMessage $message)
    {
        $messageProps = $message->get_properties();
        $correlationId = $messageProps['correlation_id'] ?? '';
        $appId = $messageProps['app_id'] ?? '';

        $this->logger->info('Received message from Rabbit', [$correlationId, $appId, $message->body]);
        $amqpSerializedObject = json_decode($message->body, true);

        if ($amqpSerializedObject === false) {
            $message->nack();
            return;
        }

        $result = $this->AMQPObjectFactory->hydrate($amqpSerializedObject);

        if ($result) {
            try {
                $this->handler->handle($result);
                $this->logger->info('Acking Message', [$correlationId, $appId]);
                $message->ack();
            } catch (\Throwable $exception) {
                $this->logger->error('Handler threw an exception', [$correlationId, $appId, $message->body, $exception->getMessage(), $exception->getTrace()]);
                $message->nack();
            }

        } else {
            $this->logger->error('Failed to hydrate object', [$correlationId, $appId, $message->body]);
            $message->nack();
        }
    }
}