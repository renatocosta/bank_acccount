<?php

namespace Domains\Context\BankAccount\Domain\Model\Account;

use Domains\CrossCutting\Domain\Model\ValueObjects\Identity\FindValueIn;

final class TransactionOperation
{

    private $value;

    public function __construct(string $value)
    {
        $findValueIn = new FindValueIn($value, TransactionInfo::BANKING_OPERATIONS);
        $this->value = $findValueIn->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
    
}
