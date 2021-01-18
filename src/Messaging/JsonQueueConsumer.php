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
    private AMQPObjectFactory $AMQPObjectFactory;
    /**
     * @var callable
     */
    private $handler;

    private LoggerInterface $logger;

    public function __construct(string $queueName, AMQPChannel $channel, string $identifier, AMQPObjectFactory $AMQPObjectFactory, callable $handler, LoggerInterface  $logger)
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
        $correlationId = $message->get_properties()['correlation_id'];
        $appId = $message->get_properties()['app_id'];

        $this->logger->info('Received message from Rabbit', [$correlationId, $appId, $message->body]);
        $amqpSerializedObject = json_decode($message->body, true);

        $result = $this->AMQPObjectFactory->hydrate($amqpSerializedObject);

        if ($result) {
            try {
                call_user_func_array($this->handler, [$result]);
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