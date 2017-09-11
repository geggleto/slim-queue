<?php


namespace SlimQ;


interface JobInterface
{
    public function __invoke(array $args);
}