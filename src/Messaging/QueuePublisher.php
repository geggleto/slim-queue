<?php


namespace Space\Core\Messaging;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class QueuePublisher
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * QueuePublisher constructor.
     *
     * @param AMQPChannel $channel
     */
    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function publish($class, array $arguments)
    {
        $message = json_encode(
            [
                'job' => $class,
                'args' => $arguments
            ]
        );

        $message = new AMQPMessage($message, array('content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($message, EXCHANGE_NAME);
    }
}