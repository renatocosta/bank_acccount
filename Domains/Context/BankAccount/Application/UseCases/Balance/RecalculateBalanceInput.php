<?php

namespace Domains\Context\BankAccount\Application\UseCases\Balance;

use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;

final class RecalculateBalanceInput
{

    public $accountId;

    public $modelState;

    public function __construct(int $accountId, MessageHandler $modelState)
    {
        $this->accountId = $accountId;
        $this->modelState = $modelState;
    }
}
