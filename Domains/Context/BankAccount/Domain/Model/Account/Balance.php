<?php

namespace Domains\Context\BankAccount\Domain\Model\Account;

final class Balance
{

    public $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return sprintf('Value %s', $this->value);
    }
}
