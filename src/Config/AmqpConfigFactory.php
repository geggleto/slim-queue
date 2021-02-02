<?php
declare(strict_types=1);

namespace SimpleQueue\Config;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class AmqpConfigFactory
{
    private string $host;
    private string $port;
    private string $user;
    private string $pass;

    public function __construct(string $host, string $port, string $user, string $pass) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function getChannel($vhost): AMQPChannel {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->pass, $vhost);
        return $connection->channel();
    }

    public function getChannelAndDeclare($vhost, $exchange, $queue): AMQPChannel {
        /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
            exchange where to publish messages.
        */
        $channel = $this->getChannel($vhost);

        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $channel->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: topic
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

        $channel->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, true, false);

        $channel->queue_bind($queue, $exchange, $queue);

        return $channel;
    }
}