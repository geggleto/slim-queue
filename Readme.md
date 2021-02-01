# SimpleQueue

Library to provide developers easy to use Serialize/Hydration over AMQP protocol.

# Dependencies
`docker run -d --hostname myrabbit --name myrabbit -p 15672:15672 -p 5672:5672 rabbitmq:3.8.11-management`

### See it in action
- Use the RabbitMQ Admin panel at `http://localhost:15672`
    - Username `guest`
    - Password `quest`
- Run the cli script - `php cli/producer.php`