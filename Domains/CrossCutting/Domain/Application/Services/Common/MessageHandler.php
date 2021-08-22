<?php

namespace Domains\CrossCutting\Domain\Application\Services\Common;

class MessageHandler
{

    public $errors = [];

    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    public function addErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function addError(string $message)
    {
        array_push($this->errors, $message);
    }
}
