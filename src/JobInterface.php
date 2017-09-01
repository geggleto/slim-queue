<?php


namespace Slim\Queue;


interface JobInterface
{
    public function __invoke(array $args);
}