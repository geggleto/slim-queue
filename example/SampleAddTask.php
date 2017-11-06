<?php

use SlimQ\JobInterface;

class SampleAddTask implements JobInterface
{
    public function __invoke(array $args)
    {
        echo (int)$args[0] + (int)$args[1];

        return true;
    }
}