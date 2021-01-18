<?php


namespace SimpleQueue\Messaging;


interface AMQPObjectInterface
{
    /**
     * This method returns an array representation of the object
     * @return array
     */
    public function serialize(): array;
}