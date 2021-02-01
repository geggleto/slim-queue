<?php
declare(strict_types=1);


namespace SimpleQueue\Messaging;


interface MessageHandler
{
    public function handle($object);
}