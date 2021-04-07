<?php

class Context
{
    public string $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}