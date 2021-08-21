<?php

namespace Domains\Context\BankAccount\Application\UseCases\Account;

use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;

final class CreateAccountInput
{

    public $customerId;

    public $accountName;

    public $currentBalance;

    public $modelState;

    public function __construct(int $customerId, string $accountName, MessageHandler $modelState, float $currentBalance = 0)
    {
        $this->customerId = $customerId;
        $this->accountName = $accountName;
        $this->modelState = $modelState;
        $this->currentBalance = $currentBalance;
    }
}
