<?php
declare(strict_types=1);

namespace SimpleQueue\Config;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;

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
            name: $exchange
            type: topic
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

        $channel->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, true, false);

        //Declare the dead-letter exchange
        $channel->exchange_declare('dlx_'.$exchange, AMQPExchangeType::TOPIC, false, true, false);

        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */

        //Declare and bind the Dead Letter Queue
        $channel->queue_declare('dlq_'.$queue, false, true, false, false);

        $channel->queue_bind('dlq_'.$queue, 'dlx_'.$exchange, $queue);

        //Declare and bind the queue and link it to the dead letter queue
        $channel->queue_declare($queue, false, true, false, false, false, new AMQPTable([
            'x-dead-letter-exchange' => 'dlx_'.$exchange,
            'x-dead-letter-routing-key' => $queue
        ]));

        $channel->queue_bind($queue, $exchange, $queue);

        //Return the instantiated channel
        return $channel;
    }
}