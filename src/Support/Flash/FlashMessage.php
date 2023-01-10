<?php

namespace Support\Flash;

class FlashMessage
{
    public function __construct(protected string $message, protected $class)
    {
    }

    public function message(): string
    {
        return $this->message;
    }

    public function class(): string
    {
        return $this->class;
    }
}
