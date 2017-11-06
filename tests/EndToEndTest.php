<?php


namespace Tests\SlimQ;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Expectation;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Container\ContainerInterface;
use SlimQ\Messaging\QueueConsumer;
use SlimQ\Messaging\QueuePublisher;

class EndToEndTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    public function testPublisher()
    {
        $exchange = 'abcd';
        $class = JobClass::class;
        $arguments = ['a' => 1];

        $channel = \Mockery::mock(AMQPChannel::class);
        $channel->shouldReceive('basic_publish')
            ->withAnyArgs()->andReturn();

        $publisher = new QueuePublisher($exchange, $channel);

        $publisher->publish('', $class, $arguments);
    }

    public function testConsumer()
    {
        $jobClass = new JobClass();

        $class = JobClass::class;
        $arguments = ['a' => 1];

        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->withArgs([JobClass::class])->andReturn($jobClass);

        $channel = \Mockery::mock(AMQPChannel::class);
        $channel->shouldReceive('basic_ack')
                ->withAnyArgs()->andReturn();

        $queuemessage = json_encode(
            [
                'job' => $class,
                'args' => $arguments
            ]
        );

        $message = \Mockery::mock(AMQPMessage::class);
        $message->body = $queuemessage;
        $message->delivery_info = ['channel' => $channel, 'delivery_tag' => 'abc'];

        $consumer = new QueueConsumer('', $channel, $container);

        $consumer->start($message);

        $this->assertEquals(1, $jobClass->getArgs()['a']);
    }

    public function testBadConsumer()
    {
        $jobClass = new JobClass();

        $class = JobClass::class;
        $arguments = ['a' => 1];

        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->withArgs([JobClass::class.'1'])->andThrow(\Exception::class);

        $channel = \Mockery::mock(AMQPChannel::class);
        $channel->shouldReceive('basic_nack')
                ->withAnyArgs()->andReturn();

        $queuemessage = json_encode(
            [
                'job' => $class.'1',
                'args' => $arguments
            ]
        );

        $message = \Mockery::mock(AMQPMessage::class);
        $message->body = $queuemessage;
        $message->delivery_info = ['channel' => $channel, 'delivery_tag' => 'abc'];

        $consumer = new QueueConsumer('', $channel, $container);

        $consumer->start($message);
    }

}