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