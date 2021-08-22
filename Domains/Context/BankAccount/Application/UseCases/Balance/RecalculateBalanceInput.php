<?php

namespace Domains\Context\BankAccount\Application\UseCases\Balance;

use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;

final class RecalculateBalanceInput
{

    public $accountId;

    public $operation;

    public $newAmount;

    public $modelState;

    public function __construct(int $accountId, string $operation, float $newAmount, MessageHandler $modelState)
    {
        $this->accountId = $accountId;
        $this->operation = $operation;
        $this->newAmount = $newAmount;
        $this->modelState = $modelState;
    }
}
