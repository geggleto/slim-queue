# SlimQ

Slim and Simple RabbitMQ Queue Producer/Consumer Library

SlimQ enables you to push messages onto a RabbitMQ queue for consumption in other parts of your system.

# Usage

Inside your PHP Web App you will use the Publisher to push messages to your queue.

Outside of your web app you will consume messages by using the QueueConsumer.

#### RabbitMQ

Newer users to RabbitMQ are encouraged to read the RabbitMQ documentation on message brokering.

### Jobs

Jobs are the object type that consume your messages. They implement the JobInterface and should return true or false.
True upon successful consumption of the message and False otherwise.

### Publishing

Publishing is easy; `$publisher->publish(MyJob::class, ['arg1'=>value])`

### Consumption

Consuming is easy; `$consumer->consume();`

### How it works

More or less the JobInterface defines an Array to use for Construction of your Job/Command/Instruction