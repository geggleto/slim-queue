<?php


namespace SlimQ\Messaging;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class QueueConsumer
{
    /** @var AMQPChannel  */
    private $channel;

    /** @var ContainerInterface  */
    private $container;

    /** @var \Ramsey\Uuid\UuidInterface */
    private $tag;

    /** @var string */
    private $queueName;

    /**
     * QueueConsumer constructor.
     *
     * @param                    $queueName
     * @param AMQPChannel        $channel
     * @param ContainerInterface $container
     */
    public function __construct($queueName, AMQPChannel $channel, ContainerInterface $container)
    {
        $this->channel = $channel;
        $this->container = $container;
        $this->tag = Uuid::uuid4();
        $this->queueName = $queueName;
    }

    public function consume()
    {
        $this->channel->basic_consume($this->queueName, $this->tag->toString(), false, false, false, false, [$this, 'start']);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

    }

    public function start(AMQPMessage $message)
    {
        $job = json_decode($message->body, true);

        try {
            $jobClass = $this->container->get($job['job']);

            $result = $jobClass($job['args']);
        } catch (\Exception $exception)
        {
            $result = false;
        }

        if ($result) {
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            return;
        }

        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
    }
}