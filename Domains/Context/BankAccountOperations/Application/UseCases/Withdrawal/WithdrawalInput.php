<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal;

use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;

final class WithdrawalInput
{

    public $id;

    public $balance;

    public $modelState;

    public function __construct(int $id, float $balance, MessageHandler $modelState)
    {
        $this->id = $id;
        $this->balance = $balance;
        $this->modelState = $modelState;
    }
}
