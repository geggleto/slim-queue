# SimpleQueue

Library to provide developers easy to use Serialize/Hydration over AMQP protocol.

### Dependencies

You will need to have Rabbit MQ running; you can do it via docker like this

`docker run -d --hostname myrabbit --name myrabbit -p 15672:15672 -p 5672:5672 rabbitmq:3.8.11-management`

### See it in action
- Run the producer script - `php cli/producer.php`
- Run the consumer script - `php cli/consumer.php`

*If you want to see it in realtime*
- Use the RabbitMQ Admin panel at `http://localhost:15672`
    - Username `guest`
    - Password `quest`
    
### Why dead-letters?
If your application nacks a message it will flow to the dead-letter queue for fault tolerance.


# Usage / Design considerations

### 1. Serialized objects

```php
class MyObject extends \SimpleQueue\Messaging\AbstractAmqpObject {
    protected static string $routingKey = 'MyQueue.Cool';

    public function getApplicationId(): ?string
    {
        return 'sf-internal-web';
    }
}
```

The only requirement is that you MUST provide a routing key. The constructor of the concrete class accepts an array as a payload.
You may use this to serialize to scalar values any object / parameters you need to send to a background worker. An example would be an Event in your system.

```php
class MyHandler implements \SimpleQueue\Messaging\MessageHandler {

    public function handle(\SimpleQueue\Messaging\AmqpObjectInterface $object)
    {
        var_dump($object->getPayload());
    }
}
```
The `$object->getPayload()` is whatever your system put into the constructor as long as it was filled with scalar values or could be jsonSerialized. 
In your Handler you can do whatever you need to for your requirements. This could be running a background task, or executing an event.

### 2. Dead-Letter Queues

By default the Config Factory will generate DLX/DLQ for you and bind them. This is recommended to allow for fault tolerance.

### Support

Find me on twitter @geggleto