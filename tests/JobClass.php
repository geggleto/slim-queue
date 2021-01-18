<?php


namespace Tests\SimpleQueue;


use SlimQ\JobInterface;

class JobClass implements JobInterface
{
    protected $args;

    public function __invoke(array $args)
    {
        $this->args = $args;

        return true;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }
}